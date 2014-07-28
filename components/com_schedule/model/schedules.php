<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Table\Table;
use Windwalker\Model\Helper\QueryHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelSchedules
 *
 * @since 1.0
 */
class ScheduleModelSchedules extends \Windwalker\Model\ListModel
{
	use \Schedule\Model\Traits\ExtendedListModelTrait;

	/**
	 * Property filterMapping.
	 *
	 * @var  array
	 */
	protected $filterMapping = array(
		'task_id'     => 'schedule.task_id',
		'member_id'   => 'map.member_id',
		'customer_id' => 'schedule.customer_id',
		'mobile'      => 'schedule.mobile',
		'tel_home'    => 'schedule.tel_home',
		'tel_office'  => 'schedule.tel_office',
		'institute_id' => 'schedule.institute_id',
		'route_id'    => 'schedule.route_id',
		'rx_id'       => 'schedule.rx_id',
		'type'        => 'schedule.type',
		'city'        => 'schedule.city',
		'area'        => 'schedule.area',
		'address_id'  => 'schedule.address_id',
		'date'        => 'schedule.date',
		'sorted'      => 'schedule.sorted',
		'deliver_nth' => 'schedule.deliver_nth',
		'drug_empty_date' => 'schedule.drug_empty_date',
		'session'     => 'schedule.session',
		'ice'         => 'schedule.ice',
		'expense'     => 'schedule.expense',
		'status'      => 'schedule.status',
		'cancel'      => 'schedule.cancel',
		'notify'      => 'schedule.notify'
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$this->addTable('schedule', Table::SCHEDULES)
			->addTable('map', Table::CUSTOMER_MEMBER_MAPS, 'schedule.customer_id = map.customer_id');

		$this->mergeFilterFields();
	}

	/**
	 * populateState
	 *
	 * @param string $ordering
	 * @param string $direction
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$input = $this->getContainer()->get('input');

		// Set filters
		foreach ($this->filterMapping as $request => $field)
		{
			$_REQUEST['filter'][$field] = $input->get($request);
		}

		parent::populateState($ordering, $direction);
	}

	/**
	 * postGetQuery
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
		// Reset select to avoid redundant columns
		$query->clear('select')
			->select('schedule.*, map.member_id')
			->group('schedule.id');
	}
}
