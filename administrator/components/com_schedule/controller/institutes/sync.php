<?php

/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Compare\InCompare;
use Windwalker\Controller\Admin\AbstractRedirectController;
use Schedule\Icrm\SdkHelper;
use Windwalker\Data\Data;
use Windwalker\Helper\ArrayHelper;
use Schedule\Table\Table;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Config\ConfigHelper;

/**
 * Class ScheduleControllerInstitutesSync
 *
 * @since 1.0
 */
class ScheduleControllerInstitutesSync extends AbstractRedirectController
{
	/**
	 * Property cities.
	 *
	 * @var array
	 */
	protected $cities;

	/**
	 * Property areas.
	 *
	 * @var array
	 */
	protected $areas;

	/**
	 * Method to run this controller.
	 *
	 * @return  mixed  A rendered view or true
	 */
	protected function doExecute()
	{
		set_time_limit(0);

		$this->cities = $this->getCities();
		$this->areas = $this->getAreas();

		$sdk = SdkHelper::getSdk();

		$params = ConfigHelper::getParams('com_schedule');

		$username = $params->get('icrm_api.username', '');
		$password = $params->get('icrm_api.password', '');

		// Get session_key
		$result = $sdk->execute('/user/login', array('username' => $username, 'password' => $password));
		$sessionKey = $result['session_key'];

		$query = array(
			'session_key' => $sessionKey,
			'filter' => array(
				'facility.published' => 1,
				'facility.in_service' => 1,
			),
			'limit' => 500,
		);

		$result = $sdk->execute('/facilities', $query);

		$this->saveInstitutes((array) $result['items']);

		$total = $result->get('list.total', 0);
		$start = $result->get('list.start', 0);
		$limit = $result->get('list.limit', $query['limit']);

		// Get all other facilities
		for ($start = $start + $limit; $start < $total; $start += $limit)
		{
			$query['limitstart'] = $start;
			$query['list'] = array('limit' => $limit);

			$result = $sdk->execute('/facilities', $query);

			$this->saveInstitutes((array) $result['items']);
		}

		$this->setMessage(sprintf('同步完成, 已同步 %d 筆資料', $total));

		$this->redirect(JRoute::_('index.php?option=com_schedule&view=institutes', false));
	}

	/**
	 * saveInstitutes
	 *
	 * @param array $items
	 *
	 * @return  void
	 */
	protected function saveInstitutes($items)
	{
		foreach ($items as $item)
		{
			$this->saveInstitute($item);
		}
	}

	/**
	 * saveInstitute
	 *
	 * Not sync those fields
	 * - route_id
	 * - floor
	 * - delivery_weekday
	 * - last_delivery_date
	 * - color_id
	 * - color_hex
	 * - color_title
	 * - sender_id
	 * - sender_name
	 * - note
	 * - params
	 *
	 * @param \stdClass $item
	 *
	 * @return  void
	 */
	protected function saveInstitute($item)
	{
		$model = $this->getModel('Institute');
		$oldItem = $model->getItem($item->id);

		$item->city = ArrayHelper::getValue($this->cities, $item->city_title, 0);
		$item->area = ArrayHelper::getValue($this->areas, $item->area_title, 0);

		$institute = array(
			'id' => $item->id,
			'title' => $item->title,
			'short_title' => $item->inner_title,
			'tel' => $item->tel,
			'fax' => $item->fax,
			'city' => $item->city,
			'city_title' => $item->city_title,
			'area' => $item->area,
			'area_title' => $item->area_title,
			'address' => $item->address,
			'state' => 1,
		);

		$model->save($institute);

		$this->updateRelatedTables($this->findUpdateRelateTables($oldItem, $item), $item);
	}

	/**
	 * getCities
	 *
	 * @return  array
	 */
	protected function getCities()
	{
		$items = array();

		foreach ((new DataMapper(Table::CITIES))->findAll() as $item)
		{
			$items[$item['title']] = $item['id'];
		}

		return $items;
	}

	/**
	 * getAreas
	 *
	 * @return  array
	 */
	protected function getAreas()
	{
		$items = array();

		foreach ((new DataMapper(Table::AREAS))->findAll() as $item)
		{
			$items[$item['title']] = $item['id'];
		}

		return $items;
	}

	/**
	 * 找出新資料與已有資料的差異處，並決定要更新那些相關聯的 Table
	 *
	 * @param \stdClass $oldItem 已有的資料
	 * @param \stdClass $item    新資料
	 *
	 * @return array 需要更新的相關聯 Table 清單
	 */
	protected function findUpdateRelateTables($oldItem, $item)
	{
		$tables = [];

		if (!empty($oldItem->short_title) && $oldItem->short_title != $item->inner_title)
		{
			$tables[] = Table::PRESCRIPTIONS;
			$tables[] = Table::SCHEDULES;
		}

		if (!empty($oldItem->city_title) && $oldItem->city_title != $item->city_title)
		{
			$tables[] = Table::CUSTOMERS;
			$tables[] = Table::ROUTES;
			$tables[] = Table::SCHEDULES;
		}

		if (!empty($oldItem->area_title) && $oldItem->city_title != $item->area_title)
		{
			$tables[] = Table::CUSTOMERS;
			$tables[] = Table::ROUTES;
			$tables[] = Table::SCHEDULES;
		}

		if (!empty($oldItem->address) && $oldItem->address != $item->address)
		{
			$tables[] = Table::CUSTOMERS;
			$tables[] = Table::SCHEDULES;
		}

		$tables = array_unique($tables);

		return $tables;
	}

	/**
	 * 更新相關聯 Table
	 *
	 * @param array     $tables Table 清單
	 * @param \stdClass $item   新資料
	 *
	 * @return void
	 */
	protected function updateRelatedTables(array $tables, $item)
	{
		$customerMapper     = new DataMapper(Table::CUSTOMERS);
		$prescriptionMapper = new DataMapper(Table::PRESCRIPTIONS);
		$routeMapper        = new DataMapper(Table::ROUTES);
		$scheduleMapper     = new DataMapper(Table::SCHEDULES);

		foreach ($tables as $table)
		{
			switch ($table)
			{
				case Table::CUSTOMERS:
					$customerMapper->updateAll(
						new Data(
							[
								'city' => $item->city,
								'city_title' => $item->city_title,
								'area' => $item->area,
								'area_title' => $item->area_title,
								'address' => $item->address,
							]
						),
						['institute_id' => $item->id, 'type' => 'resident']
					);
					break;

				case Table::PRESCRIPTIONS:
					$prescriptionMapper->updateAll(
						new Data(
							[
								'institute_short_title' => $item->inner_title,
							]
						),
						['institute_id' => $item->id, 'type' => 'resident', 'delivered' => 0]
					);
					break;

				case Table::ROUTES:
					$routeMapper->updateAll(
						new Data(
							[
								'city' => $item->city,
								'city_title' => $item->city_title,
								'area' => $item->area,
								'area_title' => $item->area_title,
							]
						),
						['institute_id' => $item->id, 'type' => 'institute']
					);
					break;

				case Table::SCHEDULES:
					$scheduleMapper->updateAll(
						new Data(
							[
								'institute_title' => $item->inner_title,
								'city' => $item->city,
								'city_title' => $item->city_title,
								'area' => $item->area,
								'area_title' => $item->area_title,
								'address' => $item->address,
							]
						),
						['institute_id' => $item->id, (string) new InCompare('`status`', ['"scheduled"','"emergency"','"pause"'])]
					);
					break;
			}
		}
	}
}
