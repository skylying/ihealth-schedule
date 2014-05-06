<?php

namespace Schedule\Helper;

use Windwalker\Data\Data;
use Schedule\Table\Table;

/**
 * Class ScheduleHelper
 *
 * @since 1.0
 */
class ScheduleHelper
{
	/**
	 * getTargetLink
	 *
	 * @param   Data $item
	 *
	 * @return  string
	 */
	public static function getTargetLink(Data $item)
	{
		$attr = array('target' => '_blank');

		if ('individual' === $item->type
			|| ('individual' !== $item->type && $item->member_id > 0))
		{
			$url = 'index.php?option=com_schedule&task=member.edit.edit&id=' . $item->member_id;
			$text = '<span class="glyphicon glyphicon-user"></span> ' .
				$item->member_name .
				' <span class="glyphicon glyphicon-share-alt"></span>';

			return \JHtml::link($url, $text, $attr);
		}

		if ('resident' === $item->type
			|| ('resident' !== $item->type && $item->institute_id > 0))
		{
			$url = 'index.php?option=com_schedule&task=institute.edit.edit&id=' . $item->institute_id;
			$text = '<span class="glyphicon glyphicon-home"></span> ' .
				$item->institute_title .
				' <span class="glyphicon glyphicon-share-alt"></span>';

			return \JHtml::link($url, $text, $attr);
		}

		return '';
	}

	/**
	 * Calculate schedule date
	 *
	 * 取得排程日期
	 *   1. 日期 = 就醫日期 + 給藥天數 - 10天 + N天
	 *   2. N天 = 找最近的星期幾送藥
	 *   3. 排除假日
	 *
	 * @param   string  $seeDoctorDate  就醫日期
	 * @param   int     $period         給藥天數
	 * @param   string  $weekday        星期幾送藥 ('MON','TUE','WED','THU','FRI','SAT','SUN')
	 *
	 * @return  string
	 */
	public static function calculateSendDate($seeDoctorDate, $period, $weekday = '')
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$weekday = strtoupper($weekday);
		$weekdays = array('MON','TUE','WED','THU','FRI','SAT','SUN');
		$maxHolidays = 3;
		$maxSearchDays = 30;
		$startAt = strtotime($seeDoctorDate) + (86400 * ($period - 10));

		// Get default weekday
		if (!in_array($weekday, $weekdays))
		{
			$weekday = $weekdays[0];
		}

		// Get holidays
		$query->select('`date`')
			->from(Table::HOLIDAYS)
			->where('`state`=1')
			->where('`weekday`=' . $db->q($weekday))
			->where('`date`>' . $db->q(date('Y-m-d', $startAt)));

		// Convert date string to timestamp
		$holidays = array_map(
			function ($val)
			{
				return strtotime($val);
			},
			$db->setQuery($query, 0, $maxHolidays)->loadColumn()
		);

		// Shift to first day
		for ($i = 0; $i < $maxSearchDays; ++$i, $startAt += 86400)
		{
			if (strtoupper(date('D', $startAt)) === $weekday)
			{
				break;
			}
		}

		// Find send date
		for ($i = 0; $i < $maxSearchDays; ++$i, $startAt += (86400 * 7))
		{
			if (!in_array($startAt, $holidays))
			{
				break;
			}
		}

		return date('Y-m-d', $startAt);
	}
}
