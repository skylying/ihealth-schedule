<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Model\MemberModel;

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

		$db     = \JFactory::getDbo();
		$query  = $db->getQuery(true);
		$select = "`customer`.`id`";

		$query->select($select)
			->from("#__schedule_customers AS customer")
			->join('LEFT', $db->quoteName('#__schedule_customer_member_maps') . ' AS map ON customer.id = map.customer_id')
			->where("`map`.`member_id`= {$this->item->id}");

		$db->setQuery($query);

		$this->item->customer_id_list = $db->loadColumn();

		return $this->item;
	}
}
