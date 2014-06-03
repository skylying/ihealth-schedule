<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Model\Customer;
use Schedule\Table\Table;
use Windwalker\Joomla\DataMapper\DataMapper;

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

		// Prepare database object
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		// ==================
		// Get member id list
		$query->select('`map`.`member_id`')
			->from(Table::CUSTOMERS . ' AS customer')
			->join('LEFT', $db->quoteName(Table::CUSTOMER_MEMBER_MAPS) . ' AS map ON customer.id = map.customer_id')
			->where('`map`.`customer_id`= ' . $db->q($this->item->id));

		// Inject member ids
		$this->item->members = $db->setQuery($query)->loadColumn();

		return $this->item;
	}
}
