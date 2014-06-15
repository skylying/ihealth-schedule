<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Table\Table;
use Windwalker\DI\Container;
use Windwalker\Model\Helper\QueryHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelMember
 *
 * @since 1.0
 */
class ScheduleModelHolidays extends \Windwalker\Model\ListModel
{
	/**
	 * Property filteerFields.
	 *
	 * @var  array
	 */
	protected $filteerFields = array(
		'start_date', 'end_date'
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.holidays.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('holiday', Table::HOLIDAYS);

		$this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());
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
		parent::populateState($ordering, $direction);

		$container = $this->getContainer();

		$input = $container->get('input');

		// Prepare Start and End
		$start = $input->get('start');
		$end = $input->get('end');

		$filters = $this->state->get('filter', array());

		if ($start)
		{
			$filters['start_date'] = $start;
		}

		if ($end)
		{
			$filters['end_date'] = $end;
		}

		$this->state->set('filter', $filters);
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
		$queryHelper = $this->container->get('model.' . $this->getName() . '.helper.query');

		// Reset select to avoid redundant columns
		$query->clear('select')
			->select($queryHelper->getSelectFields(QueryHelper::COLS_WITH_FIRST));
	}

	/**
	 * configureFilters
	 *
	 * @param \Windwalker\Model\Filter\FilterHelper $filterHelper
	 *
	 * @return  void
	 */
	protected function configureFilters($filterHelper)
	{
		// For Start day
		$filterHelper->setHandler(
			'start_date',
			function($query, $field, $value)
			{
				$date = new \JDate($value);

				/** @var $query \JDatabaseQuery */
				$query->where($query->format('%n >= %q', 'date', $date->toSql()));
			}
		);

		// For End day
		$filterHelper->setHandler(
			'end_date',
			function($query, $field, $value)
			{
				$date = new \JDate($value);

				/** @var $query \JDatabaseQuery */
				$query->where($query->format('%n <= %q', 'date', $date->toSql()));
			}
		);
	}
}
