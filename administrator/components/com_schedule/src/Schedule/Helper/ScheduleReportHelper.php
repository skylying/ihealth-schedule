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
	 * getRowData
	 *
	 * @param array $filter
	 *
	 * @return  mixed
	 */
	public function getRowData($filter)
	{
		$db = \JFactory::getDbo();

		$select = [
			'`city`',
			'`city_title`',
			'`area`',
			'`area_title`',
			'`type`',
			'`institute_id`',
			'`institute_title`',
			'SUBSTR(`date`, 1, 7) AS `year_month`',
			'SUBSTR(`date`, 1, 4) AS `year`',
			'SUBSTR(`date`, 6, 2) AS `month`',
			'COUNT(*) as `amount`',
		];

		$query = $db->getQuery(true)
			->select($select)
			->from(TABLE::SCHEDULES)
			->group("`city`, `area`, `institute_id`, `type`, `year_month`")
			->having("`type` = 'resident' or `type` = 'individual'")
			->order("`year_month`, `city`, `area`, `type` DESC, `institute_id`");

		$this->extraFilter($query, $filter);

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * extraFilter
	 *
	 * @param object $query
	 * @param string $filter
	 *
	 * @return  mixed
	 */
	public function extraFilter($query, $filter)
	{
		$thisYear = date('Y');

		$defaultYearMonthStart = sprintf('%s-01-01', $thisYear);
		$defaultYearMonthEnd   = sprintf('%s-12-31', $thisYear);

		$startDate = \JArrayHelper::getValue($filter, 'date_start', $defaultYearMonthStart);
		$endDate = \JArrayHelper::getValue($filter, 'date_end', $defaultYearMonthEnd);
		$filterCity = \JArrayHelper::getValue($filter, 'city', array());

		if (!empty($filterCity))
		{
			// Borrow from dbo to make quotes for city_title is string, and will drop if use the IDs.
			$sqlWhereCity = (string) new InCompare('`city`', $filterCity);

			$query = $query->where($sqlWhereCity);
		}

		$query->where("`type` IN('individual', 'resident') ")
			->where(sprintf('`date` >= "%s"', $startDate))
			->where(sprintf('`date` <= "%s"', $endDate));
	}

	/**
	 * getData
	 *
	 * @param   array $filter
	 *
	 * @return  array
	 */
	public function getData($filter)
	{
		$rowData = $this->getRowData($filter);

		$data = array();

		foreach ($rowData as $item)
		{
			if (!isset($data[$item->year]))
			{
				$data[$item->year] = array(
					$item->city => array(
						"total" => 0,
						"city_title" => $item->city_title,
						"areas" => array(
							$item->area => array(
								"area_title" => $item->area_title,
								"institutes" => array(),
								"customers" => array(
									"months" => array_fill(0, 12, 0),
									"sub_total" => 0,
								),
							),
						),
					),
				);
			}

			if (!isset($data[$item->year][$item->city]))
			{
				$data[$item->year][$item->city] = array(
					"total" => 0,
					"city_title" => $item->city_title,
					"areas" => array(
						$item->area => array(
							"area_title" => $item->area_title,
							"institutes" => array(),
							"customers" => array(
								"months" => array_fill(0, 12, 0),
								"sub_total" => 0,
							),
						),
					),
				);
			}

			if (!isset($data[$item->year][$item->city]["areas"][$item->area]))
			{
				$data[$item->year][$item->city]["areas"][$item->area] = array(
					"area_title" => $item->area_title,
					"institutes" => array(),
					"customers" => array(
						"months" => array_fill(0, 12, 0),
						"sub_total" => 0,
					),
				);
			}

			if (!isset($data[$item->year][$item->city]["areas"][$item->area]["institutes"][$item->institute_id]) && $item->type == 'resident')
			{
				$data[$item->year][$item->city]["areas"][$item->area]["institutes"][$item->institute_id] = array(
					"title" => $item->institute_title,
					"months" => array_fill(0, 12, 0),
					"sub_total" => 0,
				);
			}

			if (!isset($data[$item->year][$item->city]["areas"][$item->area]["customers"]) && $item->type == 'individual')
			{
				$data[$item->year][$item->city]["areas"][$item->area]["customers"] = array(
					"months" => array_fill(0, 12, 0),
					"sub_total" => 0,
				);
			}


			$month = (int) $item->month;


			if ($item->type == 'individual')
			{
				$data[$item->year][$item->city]["areas"][$item->area]["customers"]["months"][$month - 1] = $item->amount;
				$data[$item->year][$item->city]["areas"][$item->area]["customers"]["sub_total"] += $item->amount;
			}
			else
			{
				$data[$item->year][$item->city]["areas"][$item->area]["institutes"][$item->institute_id]["months"][$month - 1] = $item->amount;
				$data[$item->year][$item->city]["areas"][$item->area]["institutes"][$item->institute_id]["sub_total"] += $item->amount;
			}

			$data[$item->year][$item->city]["total"] += $item->amount;

		}

		return $data;
	}
}
