<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class ScheduleControllerAuthenticateDisplay
 *
 * @since 1.0
 */
class ScheduleControllerAuthenticateDisplay extends \Windwalker\Controller\DisplayController
{
	/**
	 * Prepare execute hook.
	 *
	 * @throws \LogicException
	 * @return void
	 */
	protected function prepareExecute()
	{
		$this->view = $this->getView('Member', 'json');
	}

	/**
	 * doExecute
	 *
	 * @throws  Exception
	 * @return  mixed
	 */
	protected function doExecute()
	{
		$username = $this->input->getString('username') ? : $this->input->getString('email');
		$password = $this->input->getString('password');

		/** @throw \Exception */
		$this->getModel('Member')->authenticate($username, $password);

		return parent::doExecute();
	}
}
