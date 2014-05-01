<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;

/**
 * Class ScheduleControllerCustomerAjaxJson
 *
 * @since 1.0
 */
class ScheduleControllerCustomerAjaxJson extends DisplayController
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

		$model = $this->getModel('Customer');

		$data = $model->getItem($id);

		echo json_encode($data);

		jexit();
	}
}
