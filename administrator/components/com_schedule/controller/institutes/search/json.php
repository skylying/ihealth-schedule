<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;

class ScheduleControllerInstitutesSearchJson extends DisplayController
{
	/**
	 * This will create json response and return it
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$queryString = JFactory::getApplication()->input->getString('filter_search');

		$model = $this->getModel('Institutes');

		$state = $model->getState();

		$state->set('search', array('institute.short_title' => $queryString));

		$items = array_map(
			function ($item)
			{
				return array(
					'id'           => $item->id,
					'dropdowntext' => $item->institute_short_title,
					'color'        => $item->color_hex,
					'delivery_day' => $item->delivery_weekday,
					'floor'        => $item->floor
				);
			},
			$model->getItems()
		);

		// Preoare floor data that select2 need
		$itemsWithFloors = array();

		foreach ($items as $item)
		{
			$floorArray = explode(',', $item['floor']);

			foreach ($floorArray as $value)
			{
				$itemsWithFloors[] = array(

					/**
					 * select2 在 onchange 時會把回傳的 "id" 塞進本身的 value 裏面
					 * 但這邊因應同一間機構在 id 相同狀況下必須更新樓層欄位的需求
					 * 必須把 ajax 回傳的 "id" 做出區隔 (在這邊是後綴上 $value)
					 * 才能滿足 select2 "onchange" 的事件觸發, 否則同一間機構,
					 * 不同樓層, 再怎麼選 id 都一樣, 無法觸發 onchange 事件
					 */
					'id'           => $item['id'] . $value,

					/**
					 * 在 select2 的 onchange callback 中再把這個真正的 id 塞回 value
					 */
					'instituteid'  => $item['id'],
					'dropdowntext' => $item['dropdowntext'] . ' ' . $value,
					'color'        => $item['color'],
					'delivery_day' => $item['delivery_day'],
					'floor'        => $value
				);
			}
		}

		jexit(json_encode($itemsWithFloors));
	}
}
