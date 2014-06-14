<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Schedule\Controller\Api\ApiSaveController;
use Windwalker\Model\Exception\ValidateFailException;
use Schedule\Helper\ScheduleHelper;
use Schedule\Table\Collection as TableCollection;

/**
 * Class ScheduleControllerPrescriptionEditSave
 *
 * @since 1.0
 */
class ScheduleControllerPrescriptionEditSave extends ApiSaveController
{
	/**
	 * Property useTransaction.
	 *
	 * @var  bool
	 */
	protected $useTransaction = true;

	/**
	 * Method to do something before save.
	 *
	 * @return void
	 *
	 * @throws ValidateFailException
	 */
	protected function preSaveHook()
	{
		if (empty($this->data['schedules']) || !is_array($this->data['schedules']))
		{
			throw new ValidateFailException(['missing schedule data']);
		}

		if (empty($this->data['drugs']) || !is_array($this->data['drugs']))
		{
			$this->data['drugs'] = array();
		}

		/** @var ScheduleModelPrescription $model */
		$model        = $this->getModel();
		$drugForm     = $model->getDrugForm();
		$scheduleForm = $model->getScheduleForm();

		// Do validation with drugs
		foreach ($this->data['drugs'] as $drug)
		{
			$model->validate($drugForm, $drug);
		}

		// Do validation with schedules
		foreach ($this->data['schedules'] as $schedule)
		{
			$model->validate($scheduleForm, $schedule);

			// Validate send date
			ScheduleHelper::validateSendDate(
				$schedule['date'],
				$schedule['deliver_nth'],
				$this->data['see_dr_date'],
				$this->data['period']
			);

			// Validate address_id
			$addressTable = TableCollection::loadTable('Address', $schedule['address_id']);

			if (empty($addressTable->id))
			{
				throw new ValidateFailException(['Invalid address id']);
			}
		}

		// Restrict prescription type to "individual"
		$this->data['type'] = 'individual';

		// Set prescription default values
		$this->data['received']  = 0;
		$this->data['called']    = 0;
		$this->data['delivered'] = 0;
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
		$rxId = empty($validData['id']) ? $model->getState()->get('prescription.id') : $validData['id'];

		/** @var ScheduleModelDrug $drugModel */
		$drugModel = $this->getModel('Drug');
		/** @var ScheduleModelSchedule $scheduleModel */
		$scheduleModel = $this->getModel('Schedule');
		/** @var ScheduleModelTask $taskModel */
		$taskModel = $this->getModel('Task');

		$scheduleModel->getState()->set('form.type', 'schedule_individual');

		foreach ($this->data['schedules'] as $schedule)
		{
			$addressTable = TableCollection::loadTable('Address', $schedule['address_id']);
			$routeTable = TableCollection::loadTable(
				'Route',
				[
					'city' => $addressTable->city,
					'area' => $addressTable->area,
					'type' => 'customer',
				]
			);

			// If no route found, create one
			if (empty($routeTable->id))
			{
				$routeTable->institute_id = 0;
				$routeTable->type = 'customer';
				$routeTable->city = $addressTable->city;
				$routeTable->city_title = $addressTable->city_title;
				$routeTable->area = $addressTable->area;
				$routeTable->area_title = $addressTable->area_title;

				// TODO: 從 config 中取得 https://github.com/smstw/ihealth-schedule/issues/220
				$routeTable->sender_id = 1;
				$routeTable->sender_name = '陳藥師';
				$routeTable->weekday = 'MON';

				$routeTable->store();

				// TODO: 無 route 時要寄 EMail 通知 https://github.com/smstw/ihealth-schedule/issues/249
			}

			// Get task
			$taskTable = TableCollection::loadTable(
				'Task',
				[
					'date' => $schedule['date'],
					'sender' => $routeTable->sender_id
				]
			);

			if (empty($taskTable->id))
			{
				$taskTable->date = $schedule['date'];
				$taskTable->sender = $routeTable->sender_id;
				$taskTable->sender_name = $routeTable->sender_name;
				$taskTable->status = 0;

				$taskModel->prepareTable($taskTable);
				$taskTable->store(true);
			}

			$schedule['type'] = 'individual';
			$schedule['status'] = 'scheduled';
			$schedule['route_id'] = $routeTable->id;
			$schedule['rx_id'] = $rxId;
			$schedule['member_id'] = $validData['member_id'];
			$schedule['customer_id'] = $validData['customer_id'];
			$schedule['sender_name'] = $routeTable->sender_name;
			$schedule['task_id'] = $taskTable->id;
			$schedule['drug_empty_date'] = ScheduleHelper::getDrugEmptyDate(
				$schedule['deliver_nth'],
				$validData['see_dr_date'],
				$validData['period']
			)->toSql();

			$scheduleModel->save($schedule);

			$scheduleModel->getState()->set('schedule.id', null);
		}

		foreach ($this->data['drugs'] as $drug)
		{
			$drug['rx_id'] = $rxId;

			$drugModel->save($drug);

			$drugModel->getState()->set('drug.id', null);
		}
	}
}
