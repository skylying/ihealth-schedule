<?php
/**
 * Part of iHealth-schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class ScheduleControllerSchedulesReport
 *
 * @since 1.0
 */
class ScheduleControllerSchedulesReport extends Windwalker\Controller\Admin\AbstractRedirectController
{
	/**
	 * Set redirect to route report layout
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$input = $this->input->get('jform', '', 'array');
		$getPostData = new JRegistry($input);

		$getFilterStat = JFactory::getApplication();
		$getFilterStat->setUserState('report.filters', $getPostData);

		$this->redirect(JRoute::_('index.php?option=com_schedule&view=schedules&layout=report', false));

		return true;
	}
}
