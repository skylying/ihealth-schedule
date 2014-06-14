<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Model\AddressModel;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelAddress
 *
 * @since 1.0
 */
class ScheduleModelAddress extends AddressModel
{
	/**
	 * Flush Default Address
	 *
	 * @param   integer  $customerId
	 * @param   integer  $addressId
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
}
