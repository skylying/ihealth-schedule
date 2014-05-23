<?php
/**
 * Part of iHealth-schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class ScheduleControllerTasksRedirect
 *
 * @since 1.0
 */
class ScheduleControllerRoutesRedirect extends Windwalker\Controller\Admin\AbstractRedirectController
{
	/**
	 * Set redirect to tasks list view
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$this->redirect(JRoute::_('index.php?option=com_schedule&view=tasks', false));

		return true;
	}
}
 