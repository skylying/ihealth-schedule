<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Schedule\Controller\Api\ApiSaveController;
use Schedule\Helper\MailHelper;

/**
 * Class ScheduleControllerMemberEditSave
 *
 * @since 1.0
 */
class ScheduleControllerMemberEditSave extends ApiSaveController
{
	/**
	 * postSaveHook
	 *
	 * @param \Windwalker\Model\CrudModel $model
	 * @param array                       $validData
	 *
	 * @return array
	 */
	protected function postSaveHook($model, $validData)
	{
		MailHelper::sendRegisteredLayout($validData['email'], $validData);

		parent::postSaveHook($model, $validData);
	}
}

