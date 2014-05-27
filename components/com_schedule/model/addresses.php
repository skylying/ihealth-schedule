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
class ScheduleModelAddresses extends \Windwalker\Model\ListModel
{
	/**
	 * Property filteerFields.
	 *
	 * @var  array
	 */
	protected $filterFields = array(
		'customer_id',
		'address.customer_id'
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.addresses.helper.query', Container::FORCE_NEW);

		$this->addTable('address', Table::ADDRESSES);

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

		$customerId = (int) $input->get('customer_id');

		$filters = $this->state->get('filter', array());

		if (! empty($customerId))
		{
			$filters['customer_id'] = $customerId;
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
		$filterHelper->setHandler(
			'customer_id',
			function ($query, $field, $value)
			{
				/** @var $query \JDatabaseQuery */
				$query->where('`address`.`customer_id`=' . (int) $value);
			}
		);

		parent::configureFilters($filterHelper);
	}
}
