<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Helper\DateHelper;
use Windwalker\DI\Container;
use Windwalker\Model\Filter\FilterHelper;
use Windwalker\Model\ListModel;
use Windwalker\Helper\ArrayHelper;
use Schedule\Table\Table;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelSchedules
 *
 * @since 1.0
 */
class ScheduleModelSchedules extends ListModel
{
	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'schedule';

	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option = 'com_schedule';

	/**
	 * Property textPrefix.
	 *
	 * @var string
	 */
	protected $textPrefix = 'COM_SCHEDULE';

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'schedules';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'schedule';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'schedules';

	/**
	 * Valid filter fields or ordering.
	 *
	 * @var  array
	 */
	protected $filterFields = array(
		'schedule.date_start',
		'schedule.date_end',
		'prescription.created'
	);

	/**
	 * Get List Query
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$q = parent::getListQuery();

		$q->group("schedule.id");

		return $q;
	}

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.schedules.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('schedule', '#__schedule_schedules')
			->addTable('route', '#__schedule_routes', 'schedule.route_id = route.id')
			->addTable('memberMap', '#__schedule_customer_member_maps', 'memberMap.customer_id = schedule.customer_id')
			->addTable('prescription', '#__schedule_prescriptions', 'schedule.rx_id = prescription.id')
			->addTable('member', '#__schedule_members', 'member.id = memberMap.member_id')
			->addTable('user', '#__users', 'user.id = prescription.created_by');

		$this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());
	}

	/**
	 * Post Get Query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
		parent::postGetQuery($query);
	}

	/**
	 * populateState
	 *
	 * @param   string  $ordering
	 * @param   string  $direction
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'schedule.id', $direction = 'DESC')
	{
		$app = JFactory::getApplication();

		$filter = $app->getUserStateFromRequest('schedules.report.filter', 'report-filter', array());

		$this->state->set('report_filter', $filter);

		$now = DateHelper::getDate();

		$this->state->set('report_filter_start_date', ArrayHelper::getValue($filter, 'date_start', $now->format('Y', true) . '-01-01'));
		$this->state->set('report_filter_end_date', ArrayHelper::getValue($filter, 'date_end', $now->format('Y', true) . '-12-31'));
		$this->state->set('report_filter_city', ArrayHelper::getValue($filter, 'city', array()));

		parent::populateState($ordering, $direction);
	}

	/**
	 * processFilters
	 *
	 * @param JDatabaseQuery $query
	 * @param array          $filters
	 *
	 * @return  JDatabaseQuery
	 */
	protected function processFilters(\JDatabaseQuery $query, $filters = array())
	{
		return parent::processFilters($query, $filters);
	}

	/**
	 * configureFilters
	 *
	 * @param FilterHelper $filterHelper
	 *
	 * @return  void
	 */
	protected function configureFilters($filterHelper)
	{
		$filterHelper->setHandler(
			'schedule.date_start',
			function ($query, $field, $start)
			{
				if ($start)
				{
					$query->where('`schedule`.`date` >= ' . $query->q($start));
				}
			}
		);

		$filterHelper->setHandler(
			'prescription.created',
			function ($query, $field, $created)
			{
				if ($created)
				{
					$query->where('`prescription`.`created` LIKE ' . $query->q($created . '%'));
				}
			}
		);

		$filterHelper->setHandler(
			'schedule.date_end',
			function ($query, $field, $end)
			{
				if ($end)
				{
					$query->where('`schedule`.`date` <= ' . $query->q($end));
				}
			}
		);
	}

	/**
	 * configureSearches
	 *
	 * @param \Windwalker\Model\Filter\SearchHelper $searchHelper
	 *
	 * @return  void
	 */
	protected function configureSearches($searchHelper)
	{
	}

	/**
	 * getFormPrint
	 *
	 * @return  JForm
	 */
	public function getPrintForm()
	{
		$config = array(
			'control'   => 'report-filter',
			'load_data' => 1
		);

		$formName = 'schedules_print';

		return $this->loadForm($this->option . '.' . $formName . '.form', $formName, $config);
	}

	/**
	 * loadFormData
	 *
	 * @return  mixed
	 */
	public function loadFormData()
	{
		$data = parent::loadFormData();

		$data->date_start = $this->state->get('report_filter_start_date');
		$data->date_end = $this->state->get('report_filter_end_date');
		$data->city = $this->state->get('report_filter_city');

		return $data;
	}

	/**
	 * getDrugDetailFilterForm
	 *
	 * @return  \JForm
	 */
	public function getDrugDetailFilterForm()
	{
		return \JForm::getInstance("com_schedule.form", "drugdetailfilter");
	}

	/**
	 * getNotifies
	 *
	 * @return  stdClass[]
	 */
	public function getNotifies()
	{
		$query = $this->db->getQuery(true);

		$query->select('GROUP_CONCAT(`schedule`.`id`) AS `id`, `schedule`.`customer_name`, `schedule`.`customer_id`, `schedule`.`notify`')
			->from(Table::SCHEDULES . ' AS `schedule`')
			->leftJoin(Table::TASKS . ' AS `task` ON `schedule`.`task_id`=`task`.`id`')
			->where('`schedule`.`notify` > 0')
			->where('`task`.`status` = 0')
			->group('`schedule`.`customer_id`');

		return $this->db->setQuery($query)->loadObjectList();
	}
}
