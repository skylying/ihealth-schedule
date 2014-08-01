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
use Schedule\Table\Table;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelTasks
 *
 * @since 1.0
 */
class ScheduleModelTasks extends ListModel
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
	protected $name = 'tasks';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'task';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'tasks';

	/**
	 * Valid filter fields or ordering.
	 *
	 * @var  array
	 */
	protected $filterFields = array(
		'task.date_start',
		'task.date_end'
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.tasks.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('task', '#__schedule_tasks');

		$this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());
	}

	/**
	 * populateState
	 *
	 * @param null $ordering
	 * @param null $direction
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Build ordering prefix
		if (!$ordering)
		{
			$table = $this->getTable('Task');

			$ordering = property_exists($table, 'ordering') ? 'task.ordering' : 'task.id';

			$ordering = property_exists($table, 'catid') ? 'task.catid, ' . $ordering : $ordering;
		}

		parent::populateState($ordering, 'ASC');
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
		// If no state filter, set published >= 0
		if (!isset($filters['task.state']) && property_exists($this->getTable(), 'state'))
		{
			$query->where($query->quoteName('task.state') . ' >= 0');
		}

		$senderId = Schedule\Helper\SenderHelper::getSenderId();
		$isSender = Schedule\Helper\SenderHelper::isSenderLogin();

		if ($isSender)
		{
			$query->where('sender = ' . $senderId);
		}

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
			'task.date_start',
			function ($query, $field, $start)
			{
				if ($start)
				{
					$query->where('`task`.`date` >= ' . $query->q($start));
				}
			}
		);

		$filterHelper->setHandler(
			'task.date_end',
			function ($query, $field, $end)
			{
				if ($end)
				{
					$query->where('`task`.`date` <= ' . $query->q($end));
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
	 * The post getQuery object.
	 *
	 * @param JDatabaseQuery $query The db query object.
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
		$query->leftJoin(Table::SCHEDULES . ' AS schedule ON task.id=schedule.task_id')
			->where('schedule.id > 0')
			->group('task.id');

		parent::postGetQuery($query);
	}
}
