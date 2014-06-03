<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Model\AdminModel;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Table;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelAddress
 *
 * @since 1.0
 */
class ScheduleModelAddress extends AdminModel
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
	protected $name = 'address';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'address';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'addresses';

	/**
	 * Method to set new item ordering as first or last.
	 *
	 * @param   JTable $table    Item table to save.
	 * @param   string $position 'first' or other are last.
	 *
	 * @return  void
	 */
	public function setOrderPosition($table, $position = 'last')
	{
		parent::setOrderPosition($table, $position);
	}

	/**
	 * Flush Default Address
	 *
	 * @param integer $customerId
	 * @param integer $addressId
	 *
	 * @return  $this
	 */
	public function flushDefaultAddress($customerId, $addressId)
	{
		$q = $this->db->getQuery(true);

		$q->update(\Schedule\Table\Table::ADDRESSES)
			->set("previous = CASE WHEN id = {$addressId} THEN 1 ELSE 0 END")
			->where("customer_id = {$customerId}");

		$this->db->setQuery($q);

		return $this;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	protected function prepareTable(\JTable $table)
	{
		$cityMapper = new DataMapper(Table::CITIES);
		$areaMapper = new DataMapper(Table::AREAS);

		if (! empty($table->city))
		{
			$city = $cityMapper->findOne($table->city);

			$table->city_title = $city->title;
		}

		if (! empty($table->area))
		{
			$area = $areaMapper->findOne($table->area);

			$table->area_title = $area->title;
		}

		parent::prepareTable($table);
	}
}
