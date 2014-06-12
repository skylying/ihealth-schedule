<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Table\Table;
use Schedule\Helper\ScheduleHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelSchedulesenddate, read as schedule-send-date
 *
 * @since 1.0
 */
class ScheduleModelSchedulesenddate extends \Windwalker\Model\Model
{
	/**
	 * getItem
	 *
	 * @param null $pk
	 *
	 * @return  JDate
	 */
	public function getItem($pk = null)
	{
		return $this->calculateSendDate();
	}

	/**
	 * Call calculateSendDate Helper to get $sendDate
	 *
	 * @return  JDate
	 */
	public function calculateSendDate()
	{
		$weekday = $this->getWeekday();

		$input = $this->getContainer()->get('input');

		$nth = $input->get('nth');
		$seeDoctorDate = $input->get('see_doctor_date');
		$period = $input->get('period');

		$sendDate = ScheduleHelper::calculateSendDate($nth, $seeDoctorDate, $period, $weekday->weekday);

		return $sendDate;
	}

	/**
	 * getWeekday
	 *
	 * @return  mixed
	 */
	public function getWeekday()
	{
		$input = $this->getContainer()->get('input');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Build where condition
		$whereCondition = array(
			sprintf("`route`.`city` = %d", $input->get('city')),
			sprintf("`route`.`area` = %d", $input->get('area')),
			"`route`.`type` = 'customer'",
		);

		// Reset redundant select fields, set query
		$query->clear('select')
			->select('`route`.`weekday`')
			->from(Table::ROUTES . ' AS route')
			->where($whereCondition);

		$weekday = $db->setQuery($query)->loadObject();

		if (empty($weekday))
		{
			$weekday = new stdClass;

			// Todo: Default weekday need to get from config
			$weekday->weekday = 'MON';
		}

		return $weekday;
	}
}
