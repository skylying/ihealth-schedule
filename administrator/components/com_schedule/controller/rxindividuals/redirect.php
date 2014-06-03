<?php
/**
 * Part of iHealth-schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class ScheduleControllerRxindividualRedirect
 *
 * @since 1.0
 */
class ScheduleControllerRxindividualsRedirect extends Windwalker\Controller\Admin\AbstractRedirectController
{
	/**
	 * Set redirect to print overview layout
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$id = $this->input->getInt("id");

		$this->redirect(JRoute::_('index.php?option=com_schedule&view=rxindividual&layout=print&id=' . $id, false));

		return true;
	}
}