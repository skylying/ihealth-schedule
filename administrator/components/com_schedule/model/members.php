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
 * Class ScheduleModelMembers
 *
 * @since 1.0
 */
class ScheduleModelMembers extends ListModel
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
	protected $name = 'members';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'member';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'members';

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		/** @var \Windwalker\Model\Helper\QueryHelper $queryHelper   */
		$queryHelper = $this->getContainer()->get('model.members.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('member', '#__schedule_members')
			->addTable('map', '#__schedule_customer_member_maps', 'member.id = map.member_id')
			->addTable('customer', '#__schedule_customers', 'customer.id = map.customer_id');

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
			$table = $this->getTable('Member');

			$ordering = property_exists($table, 'ordering') ? 'member.ordering' : 'member.id';

			$ordering = property_exists($table, 'catid') ? 'member.catid, ' . $ordering : $ordering;
		}

		parent::populateState($ordering, 'ASC');
	}

	/**
	 * postGetQuery
	 *
	 * @param   JDatabaseQuery $query
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
		$query->select('GROUP_CONCAT(customer.name) AS customers_name')
			->select('GROUP_CONCAT(customer.id) AS customers_id')
			->group('member.id');
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
