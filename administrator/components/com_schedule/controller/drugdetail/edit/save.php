<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Table;
use Schedule\Helper\ImageHelper;

/**
 * Class SaveController
 *
 * @since 1.0
 */
class ScheduleControllerDrugdetailEditSave extends SaveController
{
	/**
	 * Use DB transaction or not.
	 *
	 * @var  boolean
	 */
	protected $useTransaction = true;

	/**
	 * Property scheduleModel.
	 *
	 * @var  ScheduleModelSchedule
	 */
	protected $scheduleModel;

	/**
	 * Property customer.
	 *
	 * @var  Data
	 */
	protected $customer;

	/**
	 * Prepare Execute
	 *
	 * @return  void
	 */
	protected function prepareExecute()
	{
		$this->scheduleModel = $this->getModel("Schedule");

		parent::prepareExecute();
	}

	/**
	 * Post SaveHook
	 *
	 * @param   \Windwalker\Model\CrudModel $model
	 * @param   array                       $validData
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validData)
	{
		foreach ($this->data['schedule'] as $scheduleId => $scheduleData)
		{
			$scheduleData['id'] = $scheduleId;

			$this->scheduleModel->save($scheduleData);
		}

		parent::postSaveHook($model, $validData);
	}

	/**
	 * Get Redirect Item Url
	 *
	 * @param null   $recordId
	 * @param string $urlVar
	 *
	 * @return  string
	 */
	protected function getRedirectItemUrl($recordId = null, $urlVar = 'id')
	{
		$uri  = parent::getRedirectItemUrl($recordId, $urlVar);
		$cid  = $this->input->getString("senderCid", "");
		$date = $this->input->get("date", "");
		$uri  = "{$uri}&senderCid={$cid}&date={$date}";

		return $uri;
	}
}
