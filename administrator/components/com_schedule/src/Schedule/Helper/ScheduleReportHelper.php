<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

use Schedule\Table\Table;

/**
 * Class ScheduleReportHelper
 */
class ScheduleReportHelper
{
	/**
	 * getReportData
	 *
	 * @return  mixed
	 */
	public function getReportData()
	{
		$filter = $this->filterData();

		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->select("city_title, type, institute_title, substr(`date`, 1, 7) as `year_month`, substr(`date`, 6, 2) as month, COUNT(*) as `amount`")
			->from(TABLE::SCHEDULES)
			->where("type IN('individual', 'resident') " . $filter)
			->group("city_title, institute_title, type, `year_month`")
			->order("city_title, type DESC, institute_title, `year_month`");

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * filterData
	 *
	 * @return  void
	 */
	public function filterData()
	{
		$app =& \JFactory::getApplication();
		$filters = $app->getUserState('report.filters');

		$filterStartDate = $filters->get('date_start');
		$filterEndDate = $filters->get('date_end');
		$filterCity = $filters->get('city');

		if(empty($filterCity))
		{
			$sqlWhereCity = '';
		}
		else
		{
			$inCity = "'" . implode("', '", $filterCity) . "'";
			$sqlWhereCity = sprintf("city_title IN(%s)", $inCity);
		}

		$jDate = new \JDate();

		if(empty($filterStartDate))
		{
			$filterStartDate = $jDate->year . '-' . sprintf("%02d", 1) . '-' . sprintf("%02d", 1);
		}

		if(empty($filterEndDate))
		{
			$filterEndDate = $jDate->year . '-12-31';
		}

		$sqlBetween = sprintf("date BETWEEN '%s' AND '%s'", $filterStartDate ,$filterEndDate);

		if(empty($sqlWhereCity))
		{
			$sqlFromFilter = sprintf("AND %s", $sqlBetween);
		}
		else
		{
			$sqlFromFilter = sprintf("AND %s AND %s", $sqlWhereCity, $sqlBetween);
		}

		return $sqlFromFilter;
	}

	/**
	 * reportData
	 *
	 * @return  mixed
	 */
	public function reportData()
	{
		$reports = $this->getReportData();

		$reportData = array();
		$currentCity = '';
		$currentBelong = '';
		$currentYearMonth = '';

		foreach($reports as $report)
		{
			//City
			$pastCity = $currentCity;
			$currentCity = $report->city_title;
			if($currentCity != $pastCity)
			{
				$reportData[$currentCity] = array();
			}

			//Institute or individual to belong
			$pastBelong = $currentBelong;

			if($report->type == 'individual')
			{
				$currentBelong =  '散客';
			}
			else
			{
				$currentBelong = $report->institute_title;
			}

			if($currentBelong != $pastBelong || $currentCity != $pastCity)
			{
				$reportData[$currentCity][$currentBelong] = array();
			}

			//Counting schedules in the month
			$pastYearMonth = $currentYearMonth;
			$currentYearMonth = $report->year_month;
			$MonthHtmlTd = '';
			for($month = 1; $month <= 12; $month ++)
			{
				$getYearStar = '2014';
				$getMonth = sprintf("%02d" ,$month);
				$needleYearMonth = $getYearStar . '-' . $getMonth;

				//Check it the month of count value to set numbers.
				if($needleYearMonth == $currentYearMonth)
				{
					$theSameMonthStat = 1;
				}
				else
				{
					$theSameMonthStat = 0;
				}

				if($theSameMonthStat == 0)
				{
					$theMonthTotal = 0;
				}
				else
				{
					$theMonthTotal = $report->amount;
				}

				// $month月 useful in debug job.
				$reportData[$currentCity][$currentBelong][$month . '月'][] = $theMonthTotal;
			}
		}

		return $reportData;
	}
}
