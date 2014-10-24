<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Schedule\Controller\Api\ApiSaveController;
use Schedule\Helper\Mapping\MemberCustomerHelper;

/**
 * Class ScheduleControllerCustomerEditSave
 *
 * @since 1.0
 */
class ScheduleControllerCustomerEditSave extends ApiSaveController
{
	/**
	 * preSaveHook
	 *
	 * @return  void
	 */
	protected function preSaveHook()
	{
		parent::preSaveHook();

		// Restrict the customer type to "individual"
		$this->data['type'] = 'individual';

		$this->data['state'] = '1';

		/* We do not update customer now.
		// If ID Number exists, we update it.
		if (empty($this->data['id']) && !empty($this->data['id_number']))
		{
			$customer = (new DataMapper(Table::CUSTOMERS))->findOne(['id_number' => $this->data['id_number']]);

			if (!$customer->isNull())
			{
				$this->data = array_merge($this->data, (array) $customer);
			}
		}
		*/
	}

	/**
	 * Method that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   \Windwalker\Model\CrudModel  $model      The data model object.
	 * @param   array                        $validData  The validated data.
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validData)
	{
		parent::postSaveHook($model, $validData);

		if (empty($validData['id']))
		{
			$validData['id'] = $model->getState()->get('customer.id');
		}

		/* We do not update members now
		 * Keep customer-member n:1 relation in the FrontEnd API
		 * In the BackEnd, the relation is n:n
		$mIds = (array) $this->data['member_id'];

		$members = MemberCustomerHelper::loadMembers($validData['id']);
		$memberIds = JArrayHelper::getColumn($members, 'id');

		$memberIds = array_merge($memberIds, $mIds);

		// Update customer-member mapping
		MemberCustomerHelper::updateMembers($validData['id'], $mIds);
		*/

		// Create member-customer mapping
		MemberCustomerHelper::createRelation((int) $this->data['member_id'], (int) $validData['id']);

		// Get address model
		$addressModel = $this->getModel("Address");

		// Save address
		if (! empty($this->data['addresses']) && is_array($this->data['addresses']))
		{
			foreach ($this->data['addresses'] as $address)
			{
				$address['customer_id'] = $validData['id'];

				$addressModel->save($address);
			}
		}
	}
}
