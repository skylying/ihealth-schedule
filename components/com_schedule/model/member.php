<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Model\MemberModel;
use Schedule\Table\Table;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelMember
 *
 * @since 1.0
 */
class ScheduleModelMember extends MemberModel
{
	/**
	 * Overwrite getItem in order to join mapping table
	 *
	 * @param null $pk
	 *
	 * @return  mixed
	 */
	public function getItem($pk = null)
	{
		$this->item = parent::getItem($pk);

		if (empty($this->item->id))
		{
			return $this->item;
		}

		$this->item->customers = $this->getCustomers($this->item->id);

		return $this->item;
	}

	/**
	 * getCustomers
	 *
	 * @param   int  $memberId Member id
	 *
	 * @return  array
	 */
	protected function getCustomers($memberId)
	{
		$memberId = (int) $memberId;

		if ($memberId <= 0)
		{
			return array();
		}

		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('customer.*')
			->from(Table::CUSTOMERS . ' AS customer')
			->leftJoin(Table::CUSTOMER_MEMBER_MAPS . ' AS map ON customer.id = map.customer_id')
			->where('`map`.`member_id`=' . $memberId);

		$customers = $db->setQuery($query)->loadObjectList();
		$addresses = $this->getAddresses(JArrayHelper::getColumn($customers, 'id'));

		$jsonFields = array('tel_office', 'tel_home', 'mobile', 'params');

		foreach ($customers as $customer)
		{
			// Convert JSON format fields
			foreach ($jsonFields as $field)
			{
				$customer->$field = (array) json_decode($customer->$field);
			}

			$customer->addresses = array();

			if (! empty($addresses[$customer->id]))
			{
				$customer->addresses = $addresses[$customer->id];
			}
		}

		return $customers;
	}

	/**
	 * getAddresses
	 *
	 * @param   array  $customerIds  Customer id list
	 *
	 * @return  array
	 */
	protected function getAddresses($customerIds)
	{
		if (0 === count($customerIds))
		{
			return array();
		}

		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('address.*')
			->from(Table::ADDRESSES . ' AS address')
			->where('address.customer_id IN (' . implode(',', $customerIds) . ')');

		$addresses = array();

		foreach ($db->setQuery($query)->loadObjectList() as $address)
		{
			if (! isset($addresses[$address->customer_id]))
			{
				$addresses[$address->customer_id] = array();
			}

			$addresses[$address->customer_id][] = $address;
		}

		return $addresses;
	}
}
