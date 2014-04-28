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
 * Class ScheduleModelCustomers
 *
 * @since 1.0
 */
class ScheduleModelCustomers extends ListModel
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
	protected $name = 'customers';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'customer';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'customers';

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected $filterFields = array(
		'customers.age_start',
		'customers.age_end'
	);

	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.customers.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('customer', '#__schedule_customers')
					->addTable('map', '#__schedule_customer_member_maps', 'customer.id = customer_id ')
					->addTable('member', '#__schedule_members', 'member.id = map.member_id ')
					->addTable('institute', '#__schedule_institutes', '`customer`.`institute_id` = `institute`.`id` ');



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
			$table = $this->getTable('Customer');

			$ordering = property_exists($table, 'ordering') ? 'customer.ordering' : 'customer.id';

			$ordering = property_exists($table, 'catid') ? 'customer.catid, ' . $ordering : $ordering;
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
		if (!isset($filters['customer.state']) && property_exists($this->getTable(), 'state'))
		{
			$query->where($query->quoteName('customer.state') . ' >= 0');
		}

		$query->group('customer.id');

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
			'customers.age_start',
			function ($query, $field, $start)
			{
				if ($start)
				{
					$query->where('`customer`.`age` >= ' . $query->q($start));
				}
			}
		);

		$filterHelper->setHandler(
			'customers.age_end',
			function ($query, $field, $end)
			{
				if ($end)
				{
					$query->where('`customer`.`age` <= ' . $query->q($end));
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
