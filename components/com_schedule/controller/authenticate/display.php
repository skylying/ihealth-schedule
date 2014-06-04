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
	 * doExecute
	 *
	 * @throws  Exception
	 * @return  mixed
	 */
	protected function doExecute()
	{
		/** @var $model ScheduleModelMember */
		$model = $this->getModel('Member');

		$username = $this->input->getString('username');
		$password = $this->input->getString('password');

		/** @throw \Exception */
		$model->authenticate($username, $password);

		$view = $this->getView('Member', 'json');

		$view->setModel($model, true);

		return $view->render();
	}
}
 