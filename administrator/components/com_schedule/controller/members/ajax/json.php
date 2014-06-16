<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;
use Schedule\Table\Table;

/**
 * Class ScheduleControllerMembersAjaxJson
 *
 * @since 1.0
 */
class ScheduleControllerMembersAjaxJson extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$id = (int) $this->input->get('id');

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('`member`.`id`, `member`.`name`')
			->from(Table::MEMBERS . ' AS `member`')
			->leftJoin(Table::CUSTOMER_MEMBER_MAPS . ' AS `map` ON `map`.`member_id`=`member`.`id`')
			->where("`map`.`customer_id`={$id}");

		$response = json_encode($db->setQuery($query)->loadObjectList());

		JFactory::getDocument()->setMimeEncoding('application/json');

		jexit($response);
	}
}
