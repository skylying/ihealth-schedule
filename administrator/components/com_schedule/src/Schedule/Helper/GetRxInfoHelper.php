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
class GetRxInfoHelper
{
	/**
	 * getInfo
	 *
	 * @param int $RxID
	 *
	 * @return  mixed
	 */
	public static function getInfo($RxID)
	{
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
		$select  = 'schedule.tel_office,
		            schedule.tel_home,
		            schedule.mobile,
		            schedule.city_title,
		            schedule.area_title,
		            schedule. address,
		            schedule.deliver_nth';

		$query->select($select)
			->from(Table::SCHEDULES . ' AS schedule')
			->where('schedule.rx_id=' . $RxID);

		$info = $db->setQuery($query)->loadObjectList();

		return $info;
	}

	/**
	 * getCustomerNote
	 *
	 * @param int $customerID
	 *
	 * @return  mixed
	 */
	public static function getCustomerNote($customerID)
	{
		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('customer.note')
			->from(Table::CUSTOMERS . ' AS customer')
			->where('customer.id=' . $customerID);

		$note = $db->setQuery($query)->loadObjectList();

		return $note;
	}
}
