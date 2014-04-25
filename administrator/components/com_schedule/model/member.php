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
 * Class ScheduleModelMember
 *
 * @since 1.0
 */
class ScheduleModelMember extends AdminModel
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
	protected $name = 'member';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'member';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'members';

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
	 * Overwrite getItem in order to join mapping table
	 *
	 * @param null $pk
	 *
	 * @return  mixed
	 */
	public function getItem($pk = null)
	{
		$this->item = parent::getItem($pk);

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		if (empty($this->item->id))
		{
			return $this->item;
		}

		$select = "`customer`.`id` ,`customer`.`name`";

		$query->select($select)
			->from("#__schedule_customers AS customer")
			->join('LEFT', $db->quoteName('#__schedule_customer_member_maps') . ' AS map ON customer.id = map.customer_id')
			->where("`map`.`member_id`= {$this->item->id}");

		$db->setQuery($query);
		$customers = $db->loadObjectList();

		$this->item->customers = array();

		foreach ($customers as $customer)
		{
			$this->item->customers[] = $customer->id;
		}

		return $this->item;
	}
}
