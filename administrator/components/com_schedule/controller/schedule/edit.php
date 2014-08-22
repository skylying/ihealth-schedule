<?php

use Windwalker\Controller\Edit\SaveController;
use Schedule\Table\Table as Table;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Collection as TableCollection;
use Schedule\Helper\ScheduleHelper;
use Schedule\Helper\MailHelper;
use Windwalker\Model\Exception\ValidateFailException;

/**
 * Class ScheduleControllerSchedulesEdit
 *
 * @since 1.0
 */
class ScheduleControllerScheduleEdit extends SaveController
{
	/**
	 * Property cid.
	 *
	 * @var
	 */
	protected $cid;

	/**
	 * The data fields to update.
	 *
	 * @var string
	 */
	protected $stateData = array(
		'date'        => '',
		'sender_name' => '',
		'task_id'     => '',
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
	protected $useTransaction = false;

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

		$this->cid = $this->input->get('cid', array(), 'ARRAY');

		$validData = $this->validate();

		if (!isset($this->data['items']))
		{
			$this->data['items'] = array();
		}

		$items =& $this->data['items'];

		foreach ($this->cid as $id)
		{
			$id = (int) $id;

			if ($id <= 0)
			{
				continue;
			}

			$schedule = $this->model->getItem($id);

			$items[$id]['id']          = $id;
			$items[$id]['date']        = !empty($validData['date']) ? $validData['date'] : $schedule->date;
			$items[$id]['sender_name'] = !empty($validData['sender_name']) ? $validData['sender_name'] : $schedule->sender_name;
			$items[$id]['sender_id']   = !empty($validData['sender_id']) ? $validData['sender_id'] : $schedule->sender_id;

			// Get task data
			$task = $taskMapper->findOne(
				[
					'date'   => $items[$id]['date'],
					'sender' => $items[$id]['sender_id'],
				]
			);

			// If task data is not found, create a new task
			if ($task->isNull())
			{
				$taskModel = $this->getModel('Task');
				$task      = [
					'date'        => $items[$id]['date'],
					'sender'      => $items[$id]['sender_id'],
					'sender_name' => $items[$id]['sender_name'],
					'status'      => 0,
				];

				$taskModel->save($task);

				$task['id'] = $taskModel->getState()->get('task.id');

				$task = new Data($task);
			}

			$items[$id]['task_id'] = $task->id;

			$oldScheduleTable = TableCollection::loadTable('Schedule', $id);

			if (!empty($oldScheduleTable->id)
				&& ScheduleHelper::checkScheduleChanged($oldScheduleTable->getProperties(), $this->stateData)
			)
			{
				$this->sendNotifyMail[] = $id;
			}
		}
	}

	/**
	 * doSave
	 *
	 * @return  array|void
	 *
	 * @throws Exception
	 * @throws ValidateFailException
	 * @throws Exception
	 */
	protected function doSave()
	{
		$scheduleState = $this->model->getState();

		// Access check.
		if (!$this->allowSave($this->data, $this->key))
		{
			throw new \Exception(\JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
		}

		// Attempt to save the data.
		try
		{
			foreach ($this->data['items'] as $item)
			{
				$scheduleState->set('sender_id', $item['sender_id']);
				$this->model->save($item);
			}
		}
		catch (ValidateFailException $e)
		{
			throw $e;
		}
		catch (\Exception $e)
		{
			// Save the data in the session.
			$this->app->setUserState($this->context . '.data', $this->data);

			// Redirect back to the edit screen.
			throw $e;
		}

		// Set success message
		$this->setMessage(
			\JText::_(
				($this->lang->hasKey(strtoupper($this->option) . ($this->recordId == 0 && $this->app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
					? strtoupper($this->option)
					: 'JLIB_APPLICATION') . ($this->recordId == 0 && $this->app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
			),
			'message'
		);
	}

	/**
	 * postSaveHook
	 *
	 * @param \Windwalker\Model\CrudModel $model
	 * @param array                       $validDataSet
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validDataSet)
	{
		foreach ($this->sendNotifyMail as $scheduleId)
		{
			$oldScheduleTable = TableCollection::loadTable('Schedule', $scheduleId);

			$memberTable = TableCollection::loadTable('Member', $oldScheduleTable->member_id);
			$rx          = (new DataMapper(Table::PRESCRIPTIONS))->findOne($oldScheduleTable->rx_id);

			if (!empty($memberTable->email) && 'individual' === $rx->type)
			{
				$schedules  = (new DataMapper(Table::SCHEDULES))->find(array('rx_id' => $oldScheduleTable->rx_id));
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

		$this->redirect(JRoute::_('index.php?option=com_schedule&view=schedules', false));
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
		$sender       = $senderMapper->findOne($senderId);

		if ($sender->isNull())
		{
			throw new \InvalidArgumentException('無此藥師資料');
		}

		// Return validated data
		return array(
			'date'        => $date,
			'sender_id'   => $senderId,
			'sender_name' => $sender->name,
		);
	}
}
