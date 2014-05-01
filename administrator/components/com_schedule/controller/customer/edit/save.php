<?php

use Schedule\Helper\Mapping\MemberCustomerHelper;
use Windwalker\Controller\Edit\SaveController;

/**
 * Class ScheduleControllerCustomerEditSave
 *
 * @since 1.0
 */
class ScheduleControllerCustomerEditSave extends SaveController
{
	/**
	 * postSaveHook
	 *
	 * @param \Windwalker\Model\CrudModel $model
	 * @param array                       $validData
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validData)
	{
		$members = JArrayHelper::getValue($validData, 'members', array());

		if (empty($validData['id']))
		{
			$validData['id'] = $model->getState()->get('customer.id');
		}

		MemberCustomerHelper::updateMembers($validData['id'], $members);

		parent::postSaveHook($model, $validData);
	}
}
