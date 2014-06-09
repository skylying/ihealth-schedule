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
		$scheduleDataset = $this->data['schedule'];

		foreach ($scheduleDataset as $scheduleId => $scheduleData)
		{
			$scheduleData['id'] = $scheduleId;
			$scheduleData['ice'] = intval(isset($scheduleData['ice'][0]));
			$scheduleData['sorted'] = intval(isset($scheduleData['sorted'][0]));

			$this->scheduleModel->save($scheduleData);
		}

		parent::postSaveHook($model, $validData);
	}
}
