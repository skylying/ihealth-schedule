<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Helper\DateHelper;
use Schedule\Table\Table;
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
	use \Schedule\Model\Traits\ExtendedListModelTrait;

	/**
	 * Property filteerFields.
	 *
	 * @var  array
	 */
	protected $filterFields = array(
		'start_date',
		'end_date',
	);

	/**
	 * Property filterMapping.
	 *
	 * @var  array
	 */
	protected $filterMapping = array(
		'year' => 'holiday.year',
		'month' => 'holiday.month',
		'day' => 'holiday.day',
		'weekday' => 'holiday.weekday',
		'date' => 'holiday.date',
		'state' => 'holiday.state',
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$this->addTable('holiday', Table::HOLIDAYS);

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
		$container = $this->getContainer();

		$input = $container->get('input');

		// Set filters
		foreach ($this->filterMapping as $request => $field)
		{
			$_REQUEST['filter'][$field] = $input->get($request);
		}

		parent::populateState($ordering, $direction);

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
		// Reset select to avoid redundant columns
		$query->clear('select')
			->select($this->getSelectFields(QueryHelper::COLS_WITH_FIRST));
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
				$date = DateHelper::getDate($value);

				/** @var $query \JDatabaseQuery */
				$query->where($query->format('%n >= %q', 'date', $date->toSql(true)));
			}
		);

		// For End day
		$filterHelper->setHandler(
			'end_date',
			function($query, $field, $value)
			{
				$date = DateHelper::getDate($value);

				/** @var $query \JDatabaseQuery */
				$query->where($query->format('%n <= %q', 'date', $date->toSql(true)));
			}
		);
	}
}
