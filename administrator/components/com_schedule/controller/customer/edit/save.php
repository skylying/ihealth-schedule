<?php

use Schedule\Helper\Mapping\MemberCustomerHelper;
use Windwalker\Controller\Edit\SaveController;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Table\Table;

/**
 * Class ScheduleControllerCustomerEditSave
 *
 * @since 1.0
 */
class ScheduleControllerCustomerEditSave extends SaveController
{
	/**
	 * preSaveHook
	 *
	 * @return  void
	 */
	protected function preSaveHook()
	{
		// 身分證字號大寫
		$this->data['id_number'] = isset($this->data['id_number']) ? ucfirst($this->data['id_number']) : "";

		$addresses = isset($this->data['address']) ? json_decode($this->data['address']) : array();

		// 預設地址存入客戶資料
		foreach ($addresses as $address)
		{
			if ($address->previous)
			{
				$this->data['city'] = $address->city;
				$this->data['area'] = $address->area;
			}
		}

		parent::preSaveHook();
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
		// ================
		// Update members
		$members = JArrayHelper::getValue($validData, 'members', array());

		if (empty($validData['id']))
		{
			$validData['id'] = $model->getState()->get('customer.id');
		}

		MemberCustomerHelper::updateMembers($validData['id'], $members);

		// ================
		// Update address
		$cityMapper     = new DataMapper(Table::CITIES);
		$areaMapper     = new DataMapper(Table::AREAS);
		$addressMapper  = new DataMapper(Table::ADDRESSES);

		$state = $model->getState();

		// Get address model
		$addressModel = $this->getModel("Address");

		// Get address json data from form
		$createAddress = isset($validData['address']) ? json_decode($validData['address']) : array();

		// Get customer id
		$customerId = $state->get('customer.id');

		// Delete all the addresses with customer id
		$addressMapper->delete(array('customer_id' => $customerId));

		if (!empty($createAddress))
		{
			// Save addresses
			foreach ($createAddress as $addressTmp)
			{
				$city = $cityMapper->findOne($addressTmp->city);
				$area = $areaMapper->findOne($addressTmp->area);

				$addressModel->save(
					array(
						"customer_id" => $customerId,
						"city"        => $city->id,
						"city_title"  => $city->title,
						"area"        => $area->id,
						"area_title"  => $area->title,
						"address"     => $addressTmp->address,
						"previous"    => $addressTmp->previous
					)
				);
			}
		}

		parent::postSaveHook($model, $validData);
	}
}
