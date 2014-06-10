<?php
/**
 * Part of ihealth project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\Edit\ApplyController;

/**
 * Class SaveController
 *
 * @since 1.0
 */
class ScheduleControllerRxindividualEditApply extends ApplyController
{
	/**
	 * postExecute
	 *
	 * @param null $return
	 *
	 * @return  mixed|void
	 */
	public function postExecute($return = null)
	{
		$printButtonValue = $this->input->get('save-and-print');

		if (false !== $return)
		{
			$this->app->setUserState('save-and-print', $printButtonValue);
		}

		return parent::postExecute($return);
	}
}
