<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;
use Schedule\Helper\CalendarHelper;

/**
 * Class ScheduleControllerHolidaysCalendarJson
 *
 * @since 1.0
 */
class ScheduleControllerHolidaysCalendarJson extends DisplayController
{
	/**
	 * This will return calendar we need
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$queryString = JFactory::getApplication()->input;

		$year  = $queryString->get('year');
		$month = $queryString->get('month');

		// TODO:完善 ajax 功能
		//$calendar = CalendarHelper::getCalendar($year, $month);

//		$items = array(
//			'year'         => $year,
//			'month'        => $month,
//			'calendarhtml' => $calendar
//		);
//
//		jexit(json_encode($items));
	}
}
