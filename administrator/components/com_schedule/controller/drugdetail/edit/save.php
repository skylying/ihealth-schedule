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
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'schedules';

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
	 * Do save
	 *
	 * @return  array
	 */
	protected function doSave()
	{
		$form = $this->model->getForm($this->data, false);

		$schedules = array();
		$institutes = array();

		// Valid Data Schedule
		foreach ($this->data['schedules'] as $id => $schedule)
		{
			$schedule['id'] = $id;

			$validDataSchedule = $this->model->validate($form, $schedule);

			$schedules[$id] = $validDataSchedule;
		}

		if (!empty($this->data['institutes']))
		{
			// Valid Data Institute
			foreach ($this->data['institutes'] as $instituteId => $instituteDrugDetails)
			{
				$validDataDrugDetail = array();

				foreach ($instituteDrugDetails as $drugDetail)
				{
					$drugDetail['institute_id'] = $instituteId;

					$validDataDrugDetail[] = $drugDetail;
				}

				$institutes[$instituteId] = $validDataDrugDetail;
			}
		}

		$this->saveScheduleDrugDetails($schedules);
		$this->saveDrugExtraDetails($institutes);

		return array(
			'schedules' => $schedules,
			'institutes' => $institutes
		);
	}

	/**
	 * Save Schedule Drug Details
	 *
	 * post 形態如下
	 *
	 * ```
	 * {
	 *     'schedules' : [
	 *         {                   // Schedule Id
	 *             ice : 1,        // 0 沒冰品, 1 有冰品
	 *             sorted : 1,     // 0 未完成, 1 已完成
	 *             price: 333.33   // 自費金額 decimal (10,2)
	 *         },
	 *         {
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
	 * @param   array  $schedules
	 *
	 * @return  void
	 */
	protected function saveScheduleDrugDetails($schedules)
	{
		foreach ($schedules as $scheduleId => $scheduleData)
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

			$scheduleData['params'] = json_encode(['noid' => false]);

			if (isset($scheduleData['noid']))
			{
				if ($scheduleData['noid'])
				{
					$scheduleData['params'] = json_encode(['noid' => true]);
				}

				unset($scheduleData['noid']);
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
	 *     'institutes' : {
	 *         5 : [                    // Institute id
	 *             {                    // In same institute id data array
	 *                 id : 8,          // Drug Extra Detail Id
	 *                 ice : 1,         // 0 沒冰品, 1 有冰品
	 *                 sorted : 1,      // 0 未完成, 1 已完成
	 *                 price: 333.33    // 自費金額 decimal (10,2)
	 *             },
	 *             {
	 *                 id : 9,
	 *                 ice : 1,
	 *                 sorted : 1,
	 *                 price: 333.33
	 *             }
	 *         ],
	 *         6 : [
	 *             {
	 *                 id : 10,
	 *                 ice : 1,
	 *                 sorted : 1,
	 *                 price: 333.33
	 *             }
	 *         ]
	 *     }
	 * }
	 * ```
	 *
	 * @param   array  $institutes
	 *
	 * @return  void
	 */
	protected function saveDrugExtraDetails($institutes)
	{
		foreach ($institutes as $instituteId => $institute)
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

				$this->model->save($detail);
			}
		}
	}

	/**
	 * postSaveHook, used for clear userstate
	 *
	 * @param \Windwalker\Model\CrudModel $model
	 * @param array                       $validData
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validData)
	{
		parent::postSaveHook($model, $validData);

		// Remove sortedList after save
		JFactory::getApplication()->setUserState('drugdetail.sorted.list', null);
	}
}
