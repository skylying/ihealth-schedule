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
			|| ('individual' !== $item->type && ! empty($item->member_json)))
		{
			$html = array();

			$members = json_decode("[{$item->member_json}]");

			foreach ($members as $member)
			{
				$html[] = \Schedule\Helper\UiHelper::foreignLink('member', $member->name, $member->id, '', $attr);
			}

			return implode("", $html);
		}

		if ('resident' === $item->type
			|| ('resident' !== $item->type && $item->institute_id > 0))
		{
			return \Schedule\Helper\UiHelper::foreignLink('institute', $item->institute_title, $item->institute_id, '', $attr);
		}

		return '';
	}

	/**
	 * Calculate schedule date (送藥日期)
	 *
	 * 取得送藥日期
	 *   0. 第一次送藥的日期 = 就醫日期 + 3
	 *   1. 第二次和第三次送藥的日期 = 就醫日期 + (給藥天數 * (第幾次 - 1)) - 10天 + N天
	 *   2. N天 = 找最近的星期幾送藥
	 *   3. 排除假日
	 *
	 * @param   string  $nth            第幾次送藥 ('1st','2nd','3rd')
	 * @param   string  $seeDoctorDate  就醫日期
	 * @param   int     $period         給藥天數
	 * @param   string  $weekday        星期幾送藥 ('MON','TUE','WED','THU','FRI','SAT','SUN')
	 *
	 * @throws  \Exception
	 * @return  \JDate  送藥日期
	 */
	public static function calculateSendDate($nth, $seeDoctorDate, $period, $weekday = '')
	{
		$nth = (int) substr($nth, 0, 1);
		$date = new \JDate($seeDoctorDate);

		if ($nth < 0 || $nth > 3)
		{
			throw new \Exception('valid nth of delivery is 1, 2, and 3');
		}

		if (1 === $nth)
		{
			$date->modify('+3 day');

			return $date;
		}

		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$weekday = strtoupper($weekday);
		$weekdays = array('MON','TUE','WED','THU','FRI','SAT','SUN');
		$maxHolidays = 3;
		$maxSearchDays = 30;

		$date->modify(sprintf('+%s day', ($period * ($nth - 1)) - 10));

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
			->where('`date`>' . $db->q($date->format('Y-m-d', false, false)));

		// Convert date string to timestamp
		$holidays = array_map(
			function ($val)
			{
				return strtotime($val);
			},
			$db->setQuery($query, 0, $maxHolidays)->loadColumn()
		);

		// Shift to first day
		for ($i = 0; $i < $maxSearchDays; ++$i)
		{
			if (strtoupper($date->format('D', false, false)) === $weekday)
			{
				break;
			}

			$date->modify('+1 day');
		}

		// Find send date
		for ($i = 0; $i < $maxSearchDays; $i += 7)
		{
			if (!in_array($date->getTimestamp(), $holidays))
			{
				break;
			}

			$date->modify('+7 day');
		}

		return new \JDate($date->format('Y-m-d'));
	}
}
