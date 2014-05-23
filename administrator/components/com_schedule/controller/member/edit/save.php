<?php

use Schedule\Helper\Mapping\MemberCustomerHelper;
use Windwalker\Model\Exception\ValidateFailException;

/**
 * Class ScheduleControllerMemberEditSave
 *
 * @since 1.0
 */
class ScheduleControllerMemberEditSave extends \Windwalker\Controller\Edit\SaveController
{
	/**
	 * Method to do something before save.
	 *
	 * @throws  Windwalker\Model\Exception\ValidateFailException
	 * @return  void
	 */
	protected function preSaveHook()
	{
		parent::preSaveHook();

		// Check the password
		if (isset($this->data['password2']) && $this->data['password'] != $this->data['password2'])
		{
			throw new ValidateFailException(array(JText::_('JLIB_USER_ERROR_PASSWORD_NOT_MATCH')));
		}
	}

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
		$customer = JArrayHelper::getValue($validData, 'customer_id_list', array());

		if (empty($validData['id']))
		{
			$validData['id'] = $model->getState()->get('member.id');
		}

		MemberCustomerHelper::updateCustomers($validData['id'], $customer);

		parent::postSaveHook($model, $validData);
	}
}
