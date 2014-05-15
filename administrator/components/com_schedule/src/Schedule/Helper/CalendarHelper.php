<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

/**
 * Class ColorHelper for Namespace.
 *
 * @since 1.0
 */
class CalendarHelper
{
	/**
	 * getCalendar
	 *
	 * @param integer $year
	 * @param integer $month
	 * @param array   $data
	 * @param integer $tableWidth
	 *
	 * @return  \JGrid
	 */
	public static function getCalendar($year, $month, $data, $tableWidth = 300)
	{
		$offDaysInMonth = array();

		// Prepare data
		if (!empty($data[$month]))
		{
			$offDaysInMonth = $data[$month];
		}

		// Calculate total days
		$daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int) $month, $year);

		$grid = new \JGrid;

		$weekArray = array('0', '1', '2', '3', '4', '5', '6');

		$option['class'] = 'table table-bordered calendar calendar' . $year . $month;
		$option['id']    = 'calendar';
		$option['style'] = 'width: ' . $tableWidth . 'px; margin:0 auto;';

		$grid->setTableOptions($option);
		$grid->setColumns($weekArray);

		// Add HeadRow
		$grid->addRow(array('class' => 'headRow'), 1);

		foreach ($weekArray as $value)
		{
			$grid->setRowCell($value, \JText::_('COM_SCHEDULE_VIEW_HOLIDAY_WEEKDAY_' . $value), array('class' => 'center'));
		}

		// Fill the "blank days" at beginning
		$firstWeekDay = date('w', mktime(null, null, null, $month, 1, $year));

		$grid->addRow();

		for ($i = 0; $i < $firstWeekDay; $i++)
		{
			$grid->setRowCell($i, '');
		}

		// Print table body with date
		for ($i = 1; $i <= $daysInMonth; $i++)
		{
			$currentDay = date('w', mktime(null, null, null, $month, $i, $year));

			if ($currentDay == 0)
			{
				$grid->addRow();
			}

			$grid->setRowCell($currentDay, $i, array('class' => 'center', 'name' => $year . '-' . $month . '-' . $i));

			// Inject offdays
			if (!empty($offDaysInMonth))
			{
				foreach ($offDaysInMonth as $key => $value)
				{
					if ($key == $i)
					{
						$grid->setRowCell(
							$currentDay, $i, array(
								'id'    => $value->id,
								'class' => ($value->state == 1) ? 'center off' : 'center',
								'name'  => $value->date,
								'title' => $value->title
							)
						);
					}
				}
			}
		}

		// Fill the "blank days" at end
		$lastWeekDay = date('w', mktime(null, null, null, $month, $daysInMonth, $year));

		if ($lastWeekDay != 6)
		{
			for ($i = 0; $i <= 6 - $lastWeekDay; $i++)
			{
				// Set blank date on day after last day
				$grid->setRowCell($lastWeekDay + 1 + $i, '');
			}
		}

		$string = $grid->toString();

		return $string;
	}
}
