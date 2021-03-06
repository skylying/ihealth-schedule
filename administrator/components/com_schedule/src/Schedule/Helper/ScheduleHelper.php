<?php

namespace Schedule\Helper;

use Windwalker\Data\Data;
use Schedule\Table\Table;
use Windwalker\Model\Exception\ValidateFailException;
use Windwalker\Helper\DateHelper;

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

		if ($item->institute_id > 0)
		{
			return UiHelper::foreignLink('institute', $item->institute_title, $item->institute_id, '', $attr);
		}
		elseif ($item->customer_id > 0)
		{
			return UiHelper::foreignLink('member', $item->member_name, $item->member_id, '', $attr);
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
	 * @param   string  $weekday        星期幾送藥 ('MON','TUE','WED','THU','FRI')
	 *
	 * @throws  \Exception
	 * @return  \JDate  送藥日期
	 */
	public static function calculateSendDate($nth, $seeDoctorDate, $period, $weekday = '')
	{
		$nth = (int) substr($nth, 0, 1);
		$date = DateHelper::getDate($seeDoctorDate);

		if ($nth < 0 || $nth > 3)
		{
			throw new \Exception('valid nth of delivery is 1, 2, and 3');
		}

		if (1 === $nth)
		{
			$date->modify('+3 day');

			// Skip weekend
			for ($i = 0; $i < 3 && $date->dayofweek > 5; ++$i)
			{
				$date->modify('-1 day');
			}

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
			->where('`date`>=' . $db->q($date->format('Y-m-d', true, false)));

		// Convert date string to timestamp
		$holidays = array_map(
			function ($val)
			{
				return DateHelper::getDate($val)->getTimestamp();
			},
			$db->setQuery($query, 0, $maxHolidays)->loadColumn()
		);

		// Shift to first day
		for ($i = 0; $i < $maxSearchDays; ++$i)
		{
			if (strtoupper($date->format('D', true, false)) === $weekday)
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

		return $date;
	}

	/**
	 * Validate send date
	 *
	 * 合理的送藥日期 = 吃完藥日的前後10天
	 * (第一次送藥日期 = 就醫日期 ~ 3天內)
	 *
	 * @param   string  $sendDate       送藥日期
	 * @param   string  $nth            第幾次送藥 ('1st','2nd','3rd')
	 * @param   string  $seeDoctorDate  就醫日期
	 * @param   int     $period         給藥天數
	 *
	 * @return  bool
	 *
	 * @throws  ValidateFailException
	 */
	public static function validateSendDate($sendDate, $nth, $seeDoctorDate, $period)
	{
		if (! in_array($nth, ['1st', '2nd', '3rd']))
		{
			throw new ValidateFailException(['Invalid nth value']);
		}

		$nth = (int) substr($nth, 0, 1);

		// Get necessary timestamps (Unix Time)
		$sendDateUnixTime      = strtotime($sendDate);
		$seeDoctorDateUnixTime = strtotime($seeDoctorDate);
		$drugEmptyDateUnixTime = $seeDoctorDateUnixTime + (($nth - 1) * $period * 86400);

		// Get valid send date timestamps (Unix Time)
		$validSendDateUnixTimes = [];

		// Get the range of valid send dates (in days)
		$daysBefore = 1 === $nth ? -1 : 10;
		$daysAfter  = 1 === $nth ? 3 : 10;

		$now = DateHelper::getDate();

		// Fill valid send dates
		for ($i = -$daysBefore; $i <= $daysAfter; ++$i)
		{
			$unixTime = $drugEmptyDateUnixTime + $i * 86400;

			$now->setTimestamp($unixTime);

			// Get weekday, 1 (for Monday) through 7 (for Sunday)
			$weekday = (int) $now->format('N', true);

			if ($weekday !== 6 && $weekday !== 7)
			{
				$validSendDateUnixTimes[] = $unixTime;
			}
		}

		// Validate send date
		if (! in_array($sendDateUnixTime, $validSendDateUnixTimes))
		{
			throw new ValidateFailException(['Invalid send date for ' . $nth . 'th schedule']);
		}

		return true;
	}

	/**
	 * getDrugEmptyDate
	 *
	 * @param   string  $nth            第幾次送藥 ('1st','2nd','3rd')
	 * @param   string  $seeDoctorDate  就醫日期
	 * @param   int     $period         給藥天數
	 *
	 * @return  \JDate
	 *
	 * @throws  ValidateFailException
	 */
	public static function getDrugEmptyDate($nth, $seeDoctorDate, $period)
	{
		if (! in_array($nth, ['1st', '2nd', '3rd']))
		{
			throw new ValidateFailException(['Invalid nth value']);
		}

		$nth = (int) substr($nth, 0, 1);

		$date = DateHelper::getDate($seeDoctorDate);

		if (1 === $nth)
		{
			return $date;
		}

		$date->modify('+' . $period * ($nth - 1) . ' days');

		return $date;
	}

	/**
	 * Check schedule changing
	 *
	 * @param   array  $oldData
	 * @param   array  $validData
	 *
	 * @return  bool
	 */
	public static function checkScheduleChanged(array $oldData, array $validData)
	{
		if (isset($validData['address_id'])
			&& $oldData['address_id'] != $validData['address_id'])
		{
			return 'modify';
		}

		if (isset($validData['date'])
			&& $oldData['date'] != $validData['date'])
		{
			return 'modify';
		}

		if (isset($validData['session'])
			&& $oldData['session'] != $validData['session'])
		{
			return 'modify';
		}

		if (isset($validData['status'])
			&& 'deleted' === $validData['status'])
		{
			return 'cancel';
		}

		return false;
	}
}
