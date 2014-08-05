<?php

/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\Admin\AbstractRedirectController;
use Schedule\Icrm\SdkHelper;
use Windwalker\Helper\ArrayHelper;
use Schedule\Table\Table;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\System\ExtensionHelper;

/**
 * Class SaveController
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

		$params = ExtensionHelper::getParams('com_schedule');

		$username = $params->get('icrm.username', '');
		$password = $params->get('icrm.password', '');

		// Get session_key
		$result = $sdk->execute('/user/login', array('username' => $username, 'password' => $password));
		$sessionKey = $result['session_key'];

		$query = array(
			'session_key' => $sessionKey,
			'filter' => array(
				'facility.published' => 1,
				'facility.status' => 1,
				'contact.status' => 1,
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

		$this->updateInstituteState();

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
	 * @param array $item
	 *
	 * @return  void
	 */
	protected function saveInstitute($item)
	{
		$model = $this->getModel('Institute');

		$cityId = ArrayHelper::getValue($this->cities, $item->city_title, 0);
		$areaId = ArrayHelper::getValue($this->areas, $item->area_title, 0);

		$institute = array(
			'id' => $item->id,
			'title' => $item->title,
			'short_title' => $item->inner_title,
			'tel' => $item->tel,
			'fax' => $item->fax,
			'city' => $cityId,
			'city_title' => $item->city_title,
			'area' => $areaId,
			'area_title' => $item->area_title,
			'address' => $item->address,
			// Set state to "-11" to mark a updated record
			'state' => -11,
		);

		$model->save($institute);
	}

	/**
	 * updateInstituteState
	 *
	 * Step 1: If state is not "-11", set state to "0"
	 * Step 2: If state is     "-11", set state to "1"
	 *
	 * @return  void
	 */
	protected function updateInstituteState()
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->update(Table::INSTITUTES)
			->set('state = 0')
			->where('state <> -11');

		$db->setQuery($query)->execute();

		$query->clear()
			->update(Table::INSTITUTES)
			->set('state = 1')
			->where('state = -11');

		$db->setQuery($query)->execute();
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
}
