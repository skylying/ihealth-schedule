<?php
/**
 * Part of ihealth project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

// No direct access
defined('_JEXEC') or die;

use Schedule\Table\Table;

/**
 * Class getCustomerPhonesHelper
 *
 * @since 1.0
 */
class GetCustomerInfoHelper
{
	/**
	 * getPhones
	 *
	 * @param $customerID
	 *
	 * @return  mixed
	 */
	public static function getInfo($customerID)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('customer.tel_office, customer.tel_home, customer.mobile, note')
			->from(Table::CUSTOMERS . ' AS customer')
			->where('customer.id=' . $customerID);

		$Info = $db->setQuery($query)->loadObjectList();

		return $Info;
	}
}
