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
 * Class GetRxDataHelper
 *
 * @since 1.0
 */
class GetRxDataHelper
{
	/**
	 * getDrugList
	 *
	 * @param int $rxId
	 *
	 * @return  mixed
	 */
	public static function getDrugList($rxId)
	{
		$db     = \JFactory::getDbo();
		$query  = $db->getQuery(true);
		$select = array(
			'drug.hicode',
			'drug.quantity'
		);

		$query->select($select)
			->from(Table::DRUGS . ' AS drug')
			->where('drug.rx_id=' . $rxId);

		$drugs = $db->setQuery($query)->loadObjectList();

		return $drugs;
	}

	/**
	 * getRxList
	 *
	 * @param int $rxId
	 *
	 * @return  mixed
	 */
	public static function getRxList($rxId)
	{
		$db     = \JFactory::getDbo();
		$query  = $db->getQuery(true);
		$select = array(
			'schedule.tel_office',
			'schedule.tel_home',
			'schedule.mobile',
			'schedule.city_title',
			'schedule.area_title',
			'schedule.address',
			'schedule.deliver_nth',
			'schedule.drug_empty_date',
			'schedule.session',
			'schedule.date'
		);

		$query->select($select)
			->from(Table::SCHEDULES . ' AS schedule')
			->where('schedule.rx_id=' . $rxId);

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
