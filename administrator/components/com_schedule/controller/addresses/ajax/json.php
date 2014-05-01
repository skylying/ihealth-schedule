<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;
use \Schedule\Table\Table;

/**
 * Class ScheduleControllerAddressesAjaxJson
 *
 * @since 1.0
 */
class ScheduleControllerAddressesAjaxJson extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		\JFactory::getDocument()->setMimeEncoding('application/json');

		$id = $this->input->get('id');

		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('`id`, `city_title`, `area_title`, `address`')
			->from(Table::ADDRESSES . ' AS address')
			->where("`address`.`customer_id`={$id}");

		echo json_encode($db->setQuery($query)->loadObjectList());

		jexit();
	}
}
