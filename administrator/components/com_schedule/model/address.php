<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Model\AdminModel;

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
	 * @param integer $customer_id
	 * @param integer $address_id
	 *
	 * @return  $this
	 */
	public function flushDefaultAddress($customer_id, $address_id)
	{
		$db = JFactory::getDbo();

		$q = $db->getQuery(true);

		$q->update(\Schedule\Table\Table::ADDRESSES)
			->set("previous = CASE WHEN id = {$address_id} THEN 1 ELSE 0 END")
			->where("customer_id = {$customer_id}");

		$db->setQuery($q);

		return $this;
	}
}
