<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Model\Customer;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Table\Table;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelCustomer
 *
 * @since 1.0
 */
class ScheduleModelCustomer extends Customer
{
	/**
	 * getItem
	 *
	 * @param   int|null $pk
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

		// =====================
		// Get full address list
		$addressMapper = new DataMapper(Table::ADDRESSES);

		// Prepare empty string as json format
		$this->item->addresses = array();

		if (!empty($this->item->id))
		{
			$addressDataSet = $addressMapper->find(array("customer_id" => $this->item->id));

			$this->item->addresses = iterator_to_array($addressDataSet);
		}

		return $this->item;
	}
}
