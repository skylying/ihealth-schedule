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
		$db = \JFactory::getDbo();

		$select = [
			'`city`',
			'`city_title`',
			'`type`',
			'`institute_id`',
			'`institute_title`',
			'SUBSTR(`date`, 1, 7) AS `year_month`',
			'SUBSTR(`date`, 6, 2) AS `month`',
			'COUNT(*) as `amount`',
		];

		$query = $db->getQuery(true)
			->select($select)
			->from(TABLE::SCHEDULES)
			->where("`type` IN('individual', 'resident') ")
			->group("`city_title`, institute_title, type, `year_month`")
			->order("`city_title`, `type` DESC, `institute_title`, `year_month`");

		$query = $this->extraFilter($query);

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * extraFilter
	 *
	 * @param $query
	 *
	 * @return  mixed
	 */
	public function extraFilter($query)
	{
		$app = \JFactory::getApplication();
		$filters = $app->getUserState('report.filters');

		$thisYear = date('Y');

		$defaultYearMonthStart = sprintf('%s-01-01',$thisYear);
		$defaultYearMonthEnd = sprintf('%s-12-31',$thisYear);

		$startDate = $filters->get('date_start', $defaultYearMonthStart);
		$endDate = $filters->get('date_end', $defaultYearMonthEnd);
		$filterCity = $filters->get('city', array());

		if(!empty($filterCity))
		{
			//Borrow from dbo to make quotes for city_title is string, and will drop if use the IDs.
			$db = \JFactory::getDbo();
			$filterCity = $db->quote($filterCity);
			$sqlWhereCity = (string) new InCompare('`city_title`', $filterCity);
			$query = $query->where($sqlWhereCity);
		}

		$query = $query->where(sprintf('`date` >= "%s"', $startDate));

		$query = $query->where(sprintf('`date` <= "%s"', $endDate));

		return $query;
	}

	/**
	 * getDataTmp
	 * 此功能做為重構資料與邏輯,測試完即刪除
	 *
	 * @return  void
	 */
	public function getData()
	{
		$rowData = $this->getRowData();

		$data = array();

		foreach($rowData as $item)
		{
			if(!isset($data[$item->city]))
			{
				$data[$item->city] = array(
					"city_title" => $item->city_title,
					"institutes" => array(),
					"customers" => array(
						"months" => array_fill(0, 12, 0),
						"sub_total" => 0,
					),
					"total" => 0,
				);
			}

			if(!isset($data[$item->city]["institutes"][$item->institute_id]))
			{
				$data[$item->city]["institutes"][$item->institute_id] = array(
					"title" => $item->institute_title,
					"months" => array_fill(0, 12, 0),
					"sub_total" => 0,
				);
			}

			$month = (int) $item->month;

			if($item->type == 'individual')
			{
				$data[$item->city]["customers"]["months"][$month-1] = $item->amount;
				$data[$item->city]["customers"]["sub_total"] += $item->amount;
			}
			else
			{
				$data[$item->city]["institutes"][$item->institute_id]["months"][$month-1] = $item->amount;
				$data[$item->city]["institutes"][$item->institute_id]["sub_total"] += $item->amount;
			}

			$data[$item->city]["total"] += $item->amount;
		}

	return $data;
	}
}
