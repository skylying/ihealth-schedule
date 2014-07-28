<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Table\Table;
use Windwalker\Model\Helper\QueryHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelMember
 *
 * @since 1.0
 */
class ScheduleModelCustomers extends \Windwalker\Model\ListModel
{
	use \Schedule\Model\Traits\ExtendedListModelTrait;

	/**
	 * Property filterMapping.
	 *
	 * @var  array
	 */
	protected $filterMapping = array(
		'institute_id' => 'customer.institute_id',
		'route_id' => 'customer.route_id',
		'type' => 'customer.type',
		'age' => 'customer.age',
		'state' => 'customer.state',
		'city' => 'customer.city',
		'area' => 'customer.area',
		'hospital' => 'customer.hospital'
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$this->addTable('customer', Table::CUSTOMERS);

		$this->mergeFilterFields();
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
			->select($this->getSelectFields(QueryHelper::COLS_WITH_FIRST))
			->group('customer.id');
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
}
