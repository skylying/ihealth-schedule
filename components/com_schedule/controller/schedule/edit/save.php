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
use Windwalker\Model\Exception\ValidateFailException;
use Schedule\Helper\MailHelper;
use Windwalker\Data\Data;
use Schedule\Config\ConfigHelper;

/**
 * Class ScheduleControllerPrescriptionEditSave
 *
 * @since 1.0
 */
class ScheduleControllerScheduleEditSave extends ApiSaveController
{
	/**
	 * Property oldScheduleTable.
	 *
	 * @var \JTable
	 */
	protected $oldScheduleTable;

	/**
	 * Property notifyMail.
	 *
	 * @var string
	 */
	protected $notifyEmptyRouteEmails;

	/**
	 * Property notifyMail.
	 *
	 * @var string
	 */
	protected $sendNotifyEmptyRouteMail;

	/**
	 * Method to do something before save.
	 *
	 * @return void
	 *
	 * @throws ValidateFailException
	 */
	protected function preSaveHook()
	{
		if (empty($this->data['id']) || $this->data['id'] <= 0)
		{
			throw new ValidateFailException(['Schedule id should be greater than 0, ' . $this->data['id'] . 'given.']);
		}

		$taskModel = $this->getModel('Task');

		$scheduleModel = $this->getModel('Schedule');
		$scheduleModel->getState()->set('form.type', 'schedule_individual');

		$scheduleForm = $scheduleModel->getScheduleForm();
		$schedule     = $this->model->validate($scheduleForm, $this->data);

		// Get data from prescription table for sendate validation by rx_id
		$prescriptionTable = TableCollection::loadTable('Prescription', $this->data['rx_id']);

		if (empty($prescriptionTable->id))
		{
			throw new ValidateFailException(['Invalid correlative prescription id.']);
		}

		// Validate send date
		ScheduleHelper::validateSendDate(
			$this->data['date'],
			$this->data['deliver_nth'],
			$prescriptionTable->see_dr_date,
			$prescriptionTable->period
		);

		$addressTable = TableCollection::loadTable('Address', $schedule['address_id']);
		$routeTable   = TableCollection::loadTable(
			'Route',
			[
				'city' => $addressTable->city,
				'area' => $addressTable->area,
				'type' => 'customer',
			]
		);

		$this->notifyEmptyRouteEmails = MailHelper::getNotifyEmptyRouteMails();

		// If no route found, create one
		if (empty($routeTable->id))
		{
			$routeTable->institute_id = 0;
			$routeTable->type         = 'customer';
			$routeTable->city         = $addressTable->city;
			$routeTable->city_title   = $addressTable->city_title;
			$routeTable->area         = $addressTable->area;
			$routeTable->area_title   = $addressTable->area_title;

			$defaultWeekday = ConfigHelper::getParams('com_schedule')->get('sender.default_weekday', 'MON');
			$defaultSender = \Schedule\Helper\SenderHelper::getDefaultSender();

			$routeTable->sender_id   = $defaultSender['id'];
			$routeTable->sender_name = $defaultSender['sender'];
			$routeTable->weekday     = $defaultWeekday;

			$routeTable->store();

			// When user created a none exists route, send a notify email to iHealth staff
			$this->sendNotifyEmptyRouteMail = true;
		}

		// Get task
		$taskTable = TableCollection::loadTable(
			'Task',
			[
				'date'   => $schedule['date'],
				'sender' => $routeTable->sender_id
			]
		);

		if (empty($taskTable->id))
		{
			$taskTable->date        = $schedule['date'];
			$taskTable->sender      = $routeTable->sender_id;
			$taskTable->sender_name = $routeTable->sender_name;
			$taskTable->status      = 0;

			$taskModel->prepareTable($taskTable);
			$taskTable->store(true);
		}

		$this->data['route_id'] = $routeTable->id;
		$this->data['task_id']  = $taskTable->id;

		$this->oldScheduleTable = TableCollection::loadTable('Schedule', $this->data['id']);
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
		$schedule = new Data($model->getItem($this->data['id']));
		$changed = ScheduleHelper::checkScheduleChanged($this->oldScheduleTable->getProperties(), $validData);

		// When user changed a exist schedule, send a notify email to iHealth staff
		if (false !== $changed && !empty($this->notifyEmptyRouteEmails))
		{
			$mailData = array(
				'schedules' => array($schedule),
				'memberName' => $schedule['member_name'],
				'date' => $schedule['date'],
				'changed' => $changed,
			);

			MailHelper::scheduleChangeNotify($this->notifyEmptyRouteEmails, $mailData);
		}

		if ($this->sendNotifyEmptyRouteMail && !empty($this->notifyEmptyRouteEmails))
		{
			$mailData = array(
				'schedules' => array($schedule),
				'memberName' => $schedule['member_name'],
				'date' => $schedule['date'],
			);

			MailHelper::sendEmptyRouteMail($this->notifyEmptyRouteEmails, $mailData);
		}

		parent::postSaveHook($model, $validData);
	}
}
