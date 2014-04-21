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
 * Class ScheduleModelRxresidents
 *
 * @since 1.0
 */
class ScheduleModelRxresidents extends ListModel
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
	protected $name = 'rxresidents';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'rxresident';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'rxresidents';

	/**
	 * Property filterFields.
	 *
	 * @var  array
	 */
	protected $filterFields = array(
		'see_dr_date_start',
		'see_dr_date_end'
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.rxresidents.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('rxresident', '#__schedule_prescriptions')
			->addTable('author',   '#__users', 'rxresident.created_by = author.id')
			->addTable('modifier', '#__users', 'rxresident.modified_by = modifier.id');

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
			$table = $this->getTable('Rxresident');

			$ordering = property_exists($table, 'ordering') ? 'rxresident.ordering' : 'rxresident.id';

			$ordering = property_exists($table, 'catid') ? 'rxresident.catid, ' . $ordering : $ordering;
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
		if (!isset($filters['rxresident.state']) && property_exists($this->getTable(), 'state'))
		{
			$query->where($query->quoteName('rxresident.state') . ' >= 0');
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
			'see_dr_date_start',
			function ($query, $field, $min)
			{
				if ($min)
				{
					$query->where('`rxindividual`.`see_dr_date` >= ' . $query->q($min));
				}
			}
		);

		$filterHelper->setHandler(
			'see_dr_date_end',
			function($query, $field, $max)
			{
				if ($max)
				{
					$query->where('`rxindividual`.`see_dr_date` <= ' . $query->q($max));
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
