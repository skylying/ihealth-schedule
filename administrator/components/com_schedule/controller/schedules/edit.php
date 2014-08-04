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
		$scheduleMapper = new DataMapper(Table::SCHEDULES);

		$this->validData = $this->validate();

		foreach ($this->input->get('cid', array(), 'ARRAY') as $id)
		{
			if ($id > 0)
			{
				$schedule = $scheduleMapper->findOne(['id' => $id]);

				$this->stateData['date']        = !empty($this->validData['date']) ? $this->validData['date'] : $schedule->date;
				$this->stateData['sender_name'] = !empty($this->validData['sender_name']) ? $this->validData['sender_name'] : $schedule->sender_name;
				$this->stateData['sender_id']   = !empty($this->validData['sender_id']) ? $this->validData['sender_id'] : $schedule->sender_id;

				// Get task data
				$task = $taskMapper->findOne(
					[
						'date' => $this->stateData['date'],
						'sender' => $this->stateData['sender_id'],
					]
				);

				// If task data is not found, create a new task
				if ($task->isNull())
				{
					$taskModel = $this->getModel('Task');
					$task = [
						'date' => $this->stateData['date'],
						'sender' => $this->stateData['sender_id'],
						'sender_name' => $this->stateData['sender_name'],
						'status' => 0,
					];

					$taskModel->save($task);

					$task['id'] = $taskModel->getState()->get('task.id');

					$task = new Data($task);
				}

				$this->stateData['task_id'] = $task->id;

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

			if (!empty($memberTable->email) && 'individual' === $rx->type)
			{
				$schedules = (new DataMapper(Table::SCHEDULES))->find(array('rx_id' => $oldScheduleTable->rx_id));
				$drugsModel = $this->getModel('Drugs');
				$drugsModel->getState()->set('filter', array('drug.rx_id' => $oldScheduleTable->rx_id));

				$mailData = array(
					'schedules' => $schedules,
					'rx'        => $rx,
					'drugs'     => $drugsModel->getItems(),
					'member'    => $memberTable,
				);

				MailHelper::sendMailWhenScheduleChange($memberTable->email, $mailData);
			}
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
		else
		{
			if (empty($date) && empty($senderId))
			{
				throw new \InvalidArgumentException('請至少選擇一種選項修改');
			}
		}

		if (!empty($date))
		{
			// Validate date
			$testDate = explode('-', $date);

			if (count($testDate) !== 3 || !checkdate($testDate[1], $testDate[2], $testDate[0]))
			{
				throw new \InvalidArgumentException('日期輸入錯誤 "' . $date . '"');
			}
		}

		if (empty($senderId))
		{
			// If date is valid, senderId does not exist. Forget about sender_id, and return validated data.
			return array(
				'date' => $date,
			);
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
