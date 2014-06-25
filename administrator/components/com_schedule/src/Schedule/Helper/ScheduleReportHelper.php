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
			->where("`type` IN('individual', 'resident') ")
			->group("`city_title`, institute_title, type, `year_month`")
			->order("`year_month`, `city_title`, `type` DESC, `institute_title`");

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
			$db = \JFactory::getDbo();
			$filterCity = $db->quote($filterCity);
			$sqlWhereCity = (string) new InCompare('`city_title`', $filterCity);
			$query = $query->where($sqlWhereCity);
		}

		$query->where(sprintf('`date` >= "%s"', $startDate))
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
						"city_title" => $item->city_title,
						"institutes" => array(),
						"customers" => array(
							"months" => array_fill(0, 12, 0),
							"sub_total" => 0,
						),
						"total" => 0,
					)
				);
			}

			if (!isset($data[$item->year][$item->city]))
			{
				$data[$item->year][$item->city] = array(
					"city_title" => $item->city_title,
					"institutes" => array(),
					"customers" => array(
						"months" => array_fill(0, 12, 0),
						"sub_total" => 0,
					),
					"total" => 0,
				);
			}

			if (!isset($data[$item->year][$item->city]["institutes"][$item->institute_id]) && $item->type == 'resident')
			{
				$data[$item->year][$item->city]["institutes"][$item->institute_id] = array(
					"title" => $item->institute_title,
					"months" => array_fill(0, 12, 0),
					"sub_total" => 0,
				);
			}

			$month = (int) $item->month;

			if ($item->type == 'individual')
			{
				$data[$item->year][$item->city]["customers"]["months"][$month - 1] = $item->amount;
				$data[$item->year][$item->city]["customers"]["sub_total"] += $item->amount;
			}
			else
			{
				$data[$item->year][$item->city]["institutes"][$item->institute_id]["months"][$month - 1] = $item->amount;
				$data[$item->year][$item->city]["institutes"][$item->institute_id]["sub_total"] += $item->amount;
			}

			$data[$item->year][$item->city]["total"] += $item->amount;
		}

		return $data;
	}
}
