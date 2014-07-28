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
	use \Schedule\Model\Traits\ExtendedListModelTrait;

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
	 * Property filterMapping.
	 *
	 * @var  array
	 */
	protected $filterMapping = array(
		'customer_id' => 'address.customer.id',
		'city' => 'address.city',
		'area' => 'address.area',
		'previous' => 'address.previous'
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$this->addTable('address', Table::ADDRESSES);

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
		$input = $this->getContainer()->get('input');

		// Set filters
		foreach ($this->filterMapping as $request => $field)
		{
			$_REQUEST['filter'][$field] = $input->get($request);
		}

		parent::populateState($ordering, $direction);
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
