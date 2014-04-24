<?php

use Schedule\Helper\Mapping\MemberCustomerHelper;

/**
 * Class ScheduleControllerMemberEditSave
 *
 * @since 1.0
 */
class ScheduleControllerMemberEditSave extends \Windwalker\Controller\Edit\SaveController
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
		$customer = JArrayHelper::getValue($validData, 'customer', array());

		if (empty($validData['id']))
		{
			$validData['id'] = $model->getState()->get('member.id');
		}

		MemberCustomerHelper::deleteCustomers($validData['id']);
		MemberCustomerHelper::addCustomers($validData['id'], $customer);

		parent::postSaveHook($model, $validData);
	}
}
