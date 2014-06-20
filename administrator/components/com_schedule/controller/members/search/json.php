<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;

/**
 * Class ScheduleControllerMembersSearchJson
 */
class ScheduleControllerMembersSearchJson extends DisplayController
{
	/**
	 * This will create json response and return it
	 *
	 *  - Api format :
	 *   - base url : index.php?option=com_schedule
	 *   - api keys :
	 *     - task = members.search.json
	 *     - filter_search = {member name}
	 *
	 *   EX : index.php?option=com_schedule&task=members.search.json&filter_search=王大明
	 *
	 * - Returned data : (JSON FORMAT)
	 *  - "id"           = member id
	 *  - "dropdowntext" = member name
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$search = JFactory::getApplication()->input->getString('filter_search');

		$model = $this->getModel('Members');

		$model->getState()->set('search', array('member.name' => $search));

		$items = array_map(
			function ($item)
			{
				return array(
					'id'           => $item->id,
					'dropdowntext' => $item->name,
				);
			},
			$model->getItems()
		);

		jexit(json_encode($items));
	}
}
