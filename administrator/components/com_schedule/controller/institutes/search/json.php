<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;

/**
 * Class ScheduleControllerInstitutesSearchJson
 *
 * @since 1.0
 */
class ScheduleControllerInstitutesSearchJson extends DisplayController
{
	/**
	 * This will create json response and return it
	 *
	 * ## Api format
	 *   - base url : index.php?option=com_schedule
	 *   - api keys :
	 *     - task = institutes.search.json
	 *     - filter_search = {institute name}
	 *
	 * EX : index.php?option=com_schedule&task=institutes.search.json&filter_search=新北
	 *
	 * ## Returned data (JSON FORMAT)
	 *   - "id"               = institute id + floor title (for select2 use, separated by a delimiter '-')
	 *   - "short_title"      = institute short title
	 *   - "color_hex"        = color hex code
	 *   - "delivery_weekday" = delivery weekday
	 *   - "floor"            = floor title
	 *   - "city"             = city id
	 *   - "area"             = area id
	 *
	 * @return  void
	 */
	protected function doExecute()
	{
		$queryString = JFactory::getApplication()->input->getString('filter_search');

		if (empty($queryString))
		{
			jexit('[]');
		}

		$model = $this->getModel('Institutes');

		$model->getState()->set('search', array('institute.short_title' => $queryString));

		$items = array();

		foreach ($model->getItems() as $item)
		{
			$data = array(
				'color_hex'        => $item->color_hex,
				'delivery_weekday' => $item->delivery_weekday,
				'floor'            => $item->floor,
				'city'             => $item->city,
				'area'             => $item->area,
			);

			foreach (explode(',', $item->floor) as $floor)
			{
				/*
				 * select2 在 onchange 時會把回傳的 "id" 塞進本身的 value 裏面
				 * 但這邊因應同一間機構在 id 相同狀況下必須更新樓層欄位的需求
				 * 必須把 ajax 回傳的 "id" 做出區隔 (在這邊是後綴上 $value)
				 * 才能滿足 select2 "onchange" 的事件觸發, 否則同一間機構,
				 * 不同樓層, 再怎麼選 id 都一樣, 無法觸發 onchange 事件
				 */
				$data['id'] = $item->id . '-' . $floor;
				$data['short_title'] = $item->institute_short_title . ' ' . $floor;

				$items[] = $data;
			}
		}

		jexit(json_encode($items));
	}
}
