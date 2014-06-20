<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Schedule\Controller\Api\ApiSaveController;

/**
 * Class ScheduleControllerAddressEditSave
 *
 * @since 1.0
 */
class ScheduleControllerAddressEditSave extends ApiSaveController
{
	/**
	 * Method that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   ScheduleModelAddress  $model      The data model object.
	 * @param   array                 $validData  The validated data.
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validData)
	{
		parent::postSaveHook($model, $validData);

		if (1 == $validData['previous'])
		{
			$addressId = $model->getState()->get("address.id");

			$model->flushDefaultAddress($validData['customer_id'], $addressId);
		}
	}
}
