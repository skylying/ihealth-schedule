<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

use Windwalker\Helper\DateHelper;

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
	 *
	 * @return  \JGrid
	 */
	public static function getCalendar($year, $month, $data)
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

		$option['class'] = 'table table-bordered calendar calendar-' . $year . '-' . $month;

		$grid->setTableOptions($option);
		$grid->setColumns($weekArray);

		// Add HeadRow
		$grid->addRow(array('class' => 'headRow'), 1);

		foreach ($weekArray as $value)
		{
			$grid->setRowCell($value, \JText::_('COM_SCHEDULE_VIEW_HOLIDAY_WEEKDAY_' . $value), array('class' => 'center'));
		}

		$date = DateHelper::getDate();

		$date->setDate($year, $month, 1);

		// Fill the "blank days" at beginning
		$firstWeekDay = $date->format('w', true);

		$grid->addRow();

		for ($i = 0; $i < $firstWeekDay; $i++)
		{
			$grid->setRowCell($i, '');
		}

		// Print table body with date
		for ($i = 1; $i <= $daysInMonth; $i++)
		{
			$date->setDate($year, $month, $i);

			$currentDay = $date->format('w', true);

			// Add new row at Sunday
			if ($currentDay == 0)
			{
				$grid->addRow();
			}

			// Set default cell config
			$cellConfig = [];
			$cellConfig['data-date'] = $year . '-' . $month . '-' . $i;

			$className = ['center'];

			// Get off days config & class name
			if (!empty($offDaysInMonth[$i]))
			{
				$cellConfig['data-date'] = $offDaysInMonth[$i]->date;
				$cellConfig['id'] = $offDaysInMonth[$i]->id;

				if ($offDaysInMonth[$i]->state == 1)
				{
					$className[] = 'off';
				}
			}

			// Check weekends
			if ($currentDay != 0 && $currentDay != 6)
			{
				// 非週末時設為可勾選
				$className[] = 'selectable';
			}
			else
			{
				// 週末, 表示為休假日
				$className[] = 'off';
			}

			// Prevent offdays contains weekend
			$className = array_unique($className);

			$cellConfig['class'] = implode(' ', $className);

			$grid->setRowCell($currentDay, $i, $cellConfig);
		}

		$date->setDate($year, $month, $daysInMonth);

		// Fill the "blank days" at end
		$lastWeekDay = $date->format('w', true);

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
