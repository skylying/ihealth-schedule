<?php
/**
 * Part of ihealth-schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Schedule\Controller\Api\ApiSaveController;
use Schedule\Helper\ScheduleHelper;
use Schedule\Table\Collection as TableCollection;

/**
 * Class ScheduleControllerPrescriptionEditSave
 *
 * @since 1.0
 */
class ScheduleControllerScheduleEditSave extends ApiSaveController
{
	/**
	 * Method to do something before save.
	 *
	 * @return void
	 *
	 * @throws ValidateFailException
	 */
	protected function preSaveHook()
	{
		$taskModel = $this->getModel('Task');

		$scheduleModel = $this->getModel('Schedule');
		$scheduleModel->getState()->set('form.type', 'schedule_individual');

		$scheduleForm = $scheduleModel->getForm();
		$schedule = $scheduleModel->validate($scheduleForm, $this->data);

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

		$this->data['route_id'] = $routeTable->id;
		$this->data['task_id'] = $taskTable->id;
	}
}
