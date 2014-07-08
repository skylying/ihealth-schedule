<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;
use \Schedule\Table\Table;
use \Schedule\Helper\ScheduleHelper;
use \Schedule\Helper\ApiReturnCodeHelper;
use Windwalker\Joomla\DataMapper\DataMapper;

/**
 * Class ScheduleControllerRxindividualAjaxSendDate
 *
 * @since 1.0
 */
class ScheduleControllerRxindividualAjaxSendDate extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$seeDrDate = $this->input->get('see_dr_date');
		$cityId = $this->input->get('city_id');
		$areaId = $this->input->get('area_id');
		$nth = $this->input->get('nth');
		$period = $this->input->get('period');
		$weekday = $this->input->get('weekday');

		$routeMapper = new DataMapper(Table::ROUTES);

		// 撈路線
		if (! empty($cityId) && ! empty($areaId))
		{
			$route = $routeMapper->findOne(array("city" => $cityId, "area" => $areaId, "type" => "customer"));
		}

		// 沒有就醫日期 直接回傳message
		if (empty($seeDrDate))
		{
			echo json_encode(
				array(
					"message" => "請指定就醫日期",
					"type" => ApiReturnCodeHelper::ERROR_NO_SEE_DR_DATE,
					"nth" => $nth
				)
			);
		}

		if (! empty($weekday))
		{
			$result = ScheduleHelper::calculateSendDate($nth, $seeDrDate, $period, $weekday);

			echo json_encode(
				array(
					"date" => $result->format("Y-m-d"),
					"type" => ApiReturnCodeHelper::SUCCESS_ROUTE_EXIST,
					"nth" => $nth
				)
			);
		}
		else
		{
			if (! empty($route->id))
			{
				// 不給weekday 但撈到路線
				$result = ScheduleHelper::calculateSendDate($nth, $seeDrDate, $period, $route->weekday);

				echo json_encode(
					array(
						"date" => $result->format("Y-m-d"),
						"type" => ApiReturnCodeHelper::SUCCESS_ROUTE_EXIST,
						"nth" => $nth)
				);
			}
			else
			{
				// 又不給weekday 又撈不到路線
				echo json_encode(
					array(
						"message" => "宅配區域路線不存在，請指定外送藥師，外送日。",
						"type" => ApiReturnCodeHelper::ERROR_NO_ROUTE,
						"nth" => $nth
					)
				);
			}
		}

		jexit();
	}
}

