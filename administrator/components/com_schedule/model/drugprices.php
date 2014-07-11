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
 * Class ScheduleModelDrugprices
 *
 * @since 1.0
 */
class ScheduleModelDrugprices extends ListModel
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
	protected $name = 'drugprices';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'drugprice';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'drugprices';

	/**
	 * Valid filter fields or ordering.
	 *
	 * @var  array
	 */
	protected $filterFields = array(
		'drugprice.date_start',
		'drugprice.date_end'
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.drugprices.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('drugprice', '#__schedule_drug_extra_details')
			->addTable('institute', '#__schedule_institutes', 'drugprice.institute_id = institute.id')
			->addTable('task', '#__schedule_tasks', 'drugprice.task_id = task.id');

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
			$table = $this->getTable('Drugprice');

			$ordering = property_exists($table, 'ordering') ? 'drugprice.ordering' : 'drugprice.id';

			$ordering = property_exists($table, 'catid') ? 'drugprice.catid, ' . $ordering : $ordering;
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
		if (!isset($filters['drugprice.state']) && property_exists($this->getTable(), 'state'))
		{
			$query->where($query->quoteName('drugprice.state') . ' >= 0');
		}

		$query->group('drugprice.id');

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
			'drugprice.date_start',
			function ($query, $field, $start)
			{
				if ($start)
				{
					$query->where('`drugprice`.`date` >= ' . $query->q($start));
				}
			}
		);

		$filterHelper->setHandler(
			'drugprice.date_end',
			function ($query, $field, $end)
			{
				if ($end)
				{
					$query->where('`drugprice`.`date` <= ' . $query->q($end));
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
