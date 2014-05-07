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
					'title'        => $item->institute_short_title,
					'color'        => $item->color_hex,
					'delivery_day' => $item->delivery_weekday
				);
			},
			$model->getItems()
		);

		jexit(json_encode($items));
	}
}
