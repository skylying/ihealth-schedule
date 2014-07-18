<?php

use Windwalker\Controller\State\AbstractUpdateStateController;
use Schedule\Table\Table as Table;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Collection as TableCollection;
use Schedule\Helper\ScheduleHelper;
use Schedule\Helper\MailHelper;

/**
 * Class ScheduleControllerSchedulesEdit
 *
 * @since 1.0
 */
class ScheduleControllerSchedulesEdit extends AbstractUpdateStateController
{
	/**
	 * The data fields to update.
	 *
	 * @var string
	 */
	protected $stateData = array(
		'date' => '',
		'sender_name' => '',
		'task_id' => '',
	);

	/**
	 * Property validData.
	 *
	 * @var  array
	 */
	private $validData = array();

	/**
	 * Action text for translate.
	 *
	 * @var string
	 */
	protected $actionText = 'EDIT';

	/**
	 * Are we allow return?
	 *
	 * @var  boolean
	 */
	protected $allowReturn = true;

	/**
	 * Use DB transaction or not.
	 *
	 * @var  boolean
	 */
	protected $useTransaction = true;

	/**
	 * Property sendNotifyMail.
	 *
	 * @var  array
	 */
	protected $sendNotifyMail = array();

	/**
	 * Prepare execute hook.
	 *
	 * @throws \LogicException
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		$taskMapper = new DataMapper(Table::TASKS);

		$this->validData = $this->validate();

		$this->stateData['date']        = $this->validData['date'];
		$this->stateData['sender_name'] = $this->validData['sender_name'];

		// Get task data
		$task = $taskMapper->findOne(
			[
				'date' => $this->validData['date'],
				'sender' => $this->validData['sender_id'],
			]
		);

		// If task data is not found, create a new task
		if ($task->isNull())
		{
			$taskModel = $this->getModel('Task');
			$task = [
				'date' => $this->validData['date'],
				'sender' => $this->validData['sender_id'],
				'sender_name' => $this->validData['sender_name'],
				'status' => 0,
			];

			$taskModel->save($task);

			$task['id'] = $taskModel->getState()->get('task.id');

			$task = new Data($task);
		}

		$this->stateData['task_id'] = $task->id;

		foreach ($this->input->get('cid', array(), 'ARRAY') as $id)
		{
			if ($id > 0)
			{
				$oldScheduleTable = TableCollection::loadTable('Schedule', $id);

				if (! empty($oldScheduleTable->id)
					&& ScheduleHelper::checkScheduleChanged($oldScheduleTable->getProperties(), $this->stateData))
				{
					$this->sendNotifyMail[] = $id;
				}
			}
		}
	}

	/**
	 * postUpdateHook
	 *
	 * @param \Windwalker\Model\Model $model
	 *
	 * @return  void
	 */
	protected function postUpdateHook($model)
	{
		parent::postUpdateHook($model);

		foreach ($this->sendNotifyMail as $scheduleId)
		{
			$oldScheduleTable = TableCollection::loadTable('Schedule', $scheduleId);

			$memberTable = TableCollection::loadTable('Member', $oldScheduleTable->member_id);
			$rx = (new DataMapper(Table::PRESCRIPTIONS))->findOne($oldScheduleTable->rx_id);
			$schedules = (new DataMapper(Table::SCHEDULES))->find(array('rx_id' => $oldScheduleTable->rx_id));
			$drugsModel = $this->getModel('Drugs');
			$drugsModel->getState()->set('filter', array('drug.rx_id' => $oldScheduleTable->rx_id));

			$mailData = array(
				"schedules" => $schedules,
				"rx"        => $rx,
				"drugs"     => $drugsModel->getItems(),
			);

			MailHelper::sendMailWhenScheduleChange($memberTable->email, $mailData);
		}
	}

	/**
	 * validate
	 *
	 * @return  array
	 *
	 * @throws  InvalidArgumentException
	 */
	private function validate()
	{
		$date     = strtolower(trim($this->input->get('new_date', '')));
		$senderId = $this->input->getInt('new_sender_id');

		// Validate cid
		if (count($this->cid) === 0)
		{
			throw new \InvalidArgumentException('請至少選擇一個排程');
		}

		// Validate date
		$testDate = explode('-', $date);

		if (count($testDate) !== 3 || !checkdate($testDate[1], $testDate[2], $testDate[0]))
		{
			throw new \InvalidArgumentException('日期輸入錯誤 "' . $date . '"');
		}

		// Validate sender id
		if ($senderId <= 0)
		{
			throw new \InvalidArgumentException('無此藥師資料');
		}

		$senderMapper = new DataMapper(Table::SENDERS);
		$sender = $senderMapper->findOne($senderId);

		if ($sender->isNull())
		{
			throw new \InvalidArgumentException('無此藥師資料');
		}

		// Return validated data
		return array(
			'date' => $date,
			'sender_id' => $senderId,
			'sender_name' => $sender->name,
		);
	}
}
