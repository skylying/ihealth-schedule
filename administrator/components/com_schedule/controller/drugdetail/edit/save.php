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
	 * post 形態如下
	 *
	 * ```
	 * {
	 *     'schedules' : [
	 *         5 : {               // Schedule Id
	 *             ice : 1,        // 0 沒冰品, 1 有冰品
	 *             sorted : 1,     // 0 未完成, 1 已完成
	 *             price: 333.33   // type float
	 *         },
	 *         6 : {
	 *             ice : 1,
	 *             sorted : 1,
	 *             price: 888.88
	 *         }
	 *     ]
	 *     'institutes' : [
	 *         ...
	 *     ]
	 * }
	 * ```
	 *
	 * @return  void
	 */
	protected function saveScheduleDrugDetails()
	{
		foreach ($this->data['schedules'] as $scheduleId => $scheduleData)
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
	 * post 形態如下
	 *
	 * ```
	 * {
	 *     'schedules' : [
	 *         ...
	 *     ]
	 *     'institutes' : [
	 *         5 : [                    // Institute id
	 *             0 : {                // In same institute id data array
	 *                 id : 8,          // Drug Extra Detail Id
	 *                 ice : 1,         // 0 沒冰品, 1 有冰品
	 *                 sorted : 1,      // 0 未完成, 1 已完成
	 *                 price: 333.33   // type float
	 *             },
	 *             1 : {
	 *                 id : 9,
	 *                 ice : 1,
	 *                 sorted : 1,
	 *                 price: 333.33
	 *             }
	 *         ],
	 *         6 : [
	 *             0 : {
	 *                 id : 10,
	 *                 ice : 1,
	 *                 sorted : 1,
	 *                 price: 333.33
	 *             }
	 *         ]
	 *     ]
	 * }
	 * ```
	 *
	 * @param   \Windwalker\Model\CrudModel $model
	 *
	 * @return  void
	 */
	protected function saveDrugExtraDetails($model)
	{
		foreach ($this->data['institutes'] as $instituteId => $institute)
		{
			foreach ($institute as $detail)
			{
				$detail['institute_id'] = $instituteId;

				// Disable checkbox
				if (! isset($detail['ice']))
				{
					$detail['ice'] = 0;
				}

				// Disable checkbox
				if (! isset($detail['sorted']))
				{
					$detail['sorted'] = 0;
				}

				$model->save($detail);
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

		$urlValue = http_build_query(
			array(
				'layout' => 'edit',
				'date' => $date,
				'senderIds' => $ids
			)
		);

		$this->app->redirect($url . "&" . urldecode($urlValue));
	}

	/**
	 * Do save
	 *
	 * @return  array
	 */
	protected function doSave()
	{
		// 不多儲存
		return $this->data;
	}
}
