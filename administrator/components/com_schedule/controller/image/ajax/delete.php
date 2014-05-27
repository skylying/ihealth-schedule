<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Table\Table;

/**
 * Class ScheduleControllerImageAjaxDelete
 *
 * @since 1.0
 */
class ScheduleControllerImageAjaxDelete extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$id = $this->input->getInt("id");
		$imageMapper = new DataMapper(Table::IMAGES);

		if (empty($id))
		{
			echo "{}";

			jexit();
		}

		// Response true
		echo json_encode($imageMapper->delete(array("id" => $id)));

		jexit();
	}
}
