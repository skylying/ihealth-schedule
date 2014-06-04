<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Schedule\Table\Table;

/**
 * Class ScheduleModelPrescription
 *
 * @since 1.0
 */
class ScheduleModelPrescription extends \Windwalker\Model\AdminModel
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
	protected $name = 'prescription';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'prescription';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'prescriptions';

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		if (empty($item->id))
		{
			return $item;
		}

		$item->schedules = $this->getSchedules($item->id);
		$item->drugs = $this->getDrugs($item->id);

		return $item;
	}

	/**
	 * getSchedules
	 *
	 * @param   int  $rxId  Prescription id
	 *
	 * @return  array
	 */
	public function getSchedules($rxId)
	{
		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('schedule.*')
			->from(Table::SCHEDULES . ' AS schedule')
			->where('`schedule`.`rx_id`=' . (int) $rxId);

		$schedules = $db->setQuery($query)->loadObjectList();

		foreach ($schedules as $schedule)
		{
			$schedule->params = (array) json_decode($schedule->params);
		}

		return $schedules;
	}

	/**
	 * getDrugs
	 *
	 * @param   int  $rxId  Prescription id
	 *
	 * @return  array
	 */
	public function getDrugs($rxId)
	{
		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('drug.*')
			->from(Table::DRUGS . ' AS drug')
			->where('`drug`.`rx_id`=' . (int) $rxId);

		return $db->setQuery($query)->loadObjectList();
	}
}
