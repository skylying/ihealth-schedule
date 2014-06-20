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

		$members = MemberCustomerHelper::loadMembers($id);

		$response = json_encode($members);

		JFactory::getDocument()->setMimeEncoding('application/json');

		jexit($response);
	}
}
