<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use \Windwalker\Controller\DisplayController;
use \Schedule\Helper\AddressHelper;


/**
 * Class ScheduleControllerCustomerAjaxJson
 *
 * @since 1.0
 */
class ScheduleControllerCustomerAjaxAddress extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$cityId = $this->input->get('city');

		$areaList = AddressHelper::getAreaList($cityId, 'area');

		jexit($areaList);
	}
}
