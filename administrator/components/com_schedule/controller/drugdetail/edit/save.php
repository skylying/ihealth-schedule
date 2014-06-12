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
		$this->saveScheduleDrugDetails();
		$this->saveDrugExtraDetails($model);

		parent::postSaveHook($model, $validData);
	}

	/**
	 * Save Schedule Drug Details
	 *
	 * @return  void
	 */
	protected function saveScheduleDrugDetails()
	{
		foreach ($this->data['schedule'] as $scheduleId => $scheduleData)
		{
			$scheduleData['id'] = $scheduleId;

			// Disable checkbox
			if (! isset($scheduleData['ice']))
			{
				$scheduleData['ice'] = 0;
			}

			// Disable checkbox
			if (! isset($scheduleData['sorted']))
			{
				$scheduleData['sorted'] = 0;
			}

			$this->scheduleModel->save($scheduleData);
		}
	}

	/**
	 * Save Drug Extra Details
	 *
	 * @param   \Windwalker\Model\CrudModel $model
	 *
	 * @return  void
	 */
	protected function saveDrugExtraDetails($model)
	{
		foreach ($this->data['institutes'] as $institutes_id => $institutes)
		{
			foreach ($institutes as $id => $details)
			{
				if ("0hash0" == $id)
				{
					continue;
				}

				$details['institute_id'] = $institutes_id;

				// Disable checkbox
				if (! isset($details['ice']))
				{
					$details['ice'] = 0;
				}

				// Disable checkbox
				if (! isset($details['sorted']))
				{
					$details['sorted'] = 0;
				}

				show($details);

				$model->save($details);
			}
		}
	}

	/**
	 * Redirect
	 *
	 * @param string $url
	 * @param null   $msg
	 * @param string $type
	 *
	 * @return  void
	 */
	public function redirect($url, $msg = null, $type = 'message')
	{
		$ids  = $this->input->getVar("senderIds", array());
		$date = $this->input->get("date", "");
		$url  = $this->getRedirectItemUrl();

		$url = $url . "&" . urldecode(
				http_build_query(
					array(
						'senderIds' => $ids
					)
				)
			);

		$this->app->redirect("{$url}&layout=edit&date={$date}");
	}
}
