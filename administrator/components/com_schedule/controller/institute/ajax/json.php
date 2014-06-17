<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;

/**
 * Class ScheduleControllerInstituteAjaxJson
 *
 * @since 1.0
 */
class ScheduleControllerInstituteAjaxJson extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$item = $this->getModel()->getItem();

		$response = json_encode($item);

		JFactory::getDocument()->setMimeEncoding('application/json');

		jexit($response);
	}
}
