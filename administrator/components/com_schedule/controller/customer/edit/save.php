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
		// Prepare tables
		$customerMapper = new DataMapper(Table::CUSTOMERS);
		$cityMapper     = new DataMapper(Table::CITIES);
		$areaMapper     = new DataMapper(Table::AREAS);
		$addressMapper  = new DataMapper(Table::ADDRESSES);

		// Get address model
		$addressModel = $this->getModel("Address");

		// Get address json data from form
		$createAddress = isset($this->data['address']) ? json_decode($this->data['address']) : array();

		// Get customer id
		$customer = $customerMapper->findOne($this->data['id']);

		// Delete all the addresses with customer id
		$addressMapper->delete(array('customer_id' => $this->data['id']));

		if (!empty($createAddress))
		{

			// Save addresses
			foreach ($createAddress as $addressTmp)
			{
				$city = $cityMapper->findOne($addressTmp->city);
				$area = $areaMapper->findOne($addressTmp->area);

				$addressModel->save(
					array(
						"customer_id" => $customer->id,
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
		$members = JArrayHelper::getValue($validData, 'members', array());

		if (empty($validData['id']))
		{
			$validData['id'] = $model->getState()->get('customer.id');
		}

		MemberCustomerHelper::updateMembers($validData['id'], $members);

		parent::postSaveHook($model, $validData);
	}
}
