<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;
use Schedule\Helper\Mapping\MemberCustomerHelper;

/**
 * Class ScheduleControllerRxindividualAjaxMember
 *
 * @since 1.0
 */
class ScheduleControllerRxindividualAjaxMember extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$customerId = $this->input->getInt('customer_id');

		if (empty($customerId))
		{
			echo json_encode(array());

			jexit();
		}

		$members = MemberCustomerHelper::loadMembers($customerId);

		header('Content-Type: application/json');

		echo json_encode($members);

		jexit();
	}
}

