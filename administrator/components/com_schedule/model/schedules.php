<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\DI\Container;
use Windwalker\Model\Filter\FilterHelper;
use Windwalker\Model\ListModel;

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
		'schedule.date_end'
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.schedules.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('schedule', '#__schedule_schedules')
			->addTable('route', '#__schedule_routes', 'schedule.route_id = route.id');

		$this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());
	}

	/**
	 * populateState
	 *
	 * @param   string  $ordering
	 * @param   string  $direction
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'schedule.id', $direction = 'ASC')
	{
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
			'schedule.date_end',
			function ($query, $field, $end)
			{
				if ($end)
				{
					$query->where('`schedule`.`date` >= ' . $query->q($end));
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
}
