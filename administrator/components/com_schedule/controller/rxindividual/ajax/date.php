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
use Windwalker\Joomla\DataMapper\DataMapper;

/**
 * Class ScheduleControllerRxindividualAjaxDate
 *
 * @since 1.0
 */
class ScheduleControllerRxindividualAjaxDate extends DisplayController
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

		$routeMapper   = new DataMapper(Table::ROUTES);

		$route = $routeMapper->findOne(array("city" => $cityId, "area" => $areaId));

		if (! empty($route->id))
		{
			if (!empty($seeDrDate))
			{
				$result = ScheduleHelper::calculateSendDate($nth, $seeDrDate, $period, $route->weekday);

				echo json_encode((object) array("date" => $result->format("Y-m-d"), "type" => "0", "nth" => $nth));
			}
			else
			{
				echo json_encode((object) array("message" => "請指定就醫日期", "type" => "1", "nth" => $nth));
			}
		}
		else
		{
			echo json_encode((object) array("message" => "宅配區域路線不存在，請指定外送藥師，外送日。", "type" => "2", "nth" => $nth));
		}

		jexit();
	}
}

