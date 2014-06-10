<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

use Schedule\Table\Table;
use Windwalker\Compare\InCompare;

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
	public function getRowData()
	{
		$filter = $this->buildSqlFilterString();

		$db = \JFactory::getDbo();

		$select = [
			'`city_title`',
			'`type`',
			'`institute_title`',
			'SUBSTR(`date`, 1, 7) AS `year_month`',
			'SUBSTR(`date`, 6, 2) AS `month`',
			'COUNT(*) as `amount`',
		];

		$query = $db->getQuery(true)
			->select($select)
			->from(TABLE::SCHEDULES)
			->where("type IN('individual', 'resident') " . $filter)
			->group("city_title, institute_title, type, `year_month`")
			->order("city_title, type DESC, institute_title, `year_month`");

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * filterData
	 *
	 * @return  string
	 */
	public function buildSqlFilterString()
	{
		$app = \JFactory::getApplication();
		$filters = $app->getUserState('report.filters');

		$jDate = new \JDate();

		$defaultYearMonthStart = $jDate->year . '-' . sprintf("%02d", 1) . '-' . sprintf("%02d", 1);
		$defaultYearMonthEnd = $jDate->year . '-12-31';

		$filterStartDate = $filters->get('date_start', $defaultYearMonthStart);
		$filterEndDate = $filters->get('date_end', $defaultYearMonthEnd);
		$filterCity = $filters->get('city', '');

		$sqlWhereCity = '';

		if(!empty($filterCity))
		{
			$db = \JFactory::getDbo();
			$filterCity = $db->quote($filterCity);
			$sqlWhereCity = (string) new InCompare('`city`', $filterCity);
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
	public function getData()
	{
		$reports = $this->getRowData();

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
