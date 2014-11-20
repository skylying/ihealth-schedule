<?php

use Windwalker\Helper\DateHelper;
use Windwalker\Controller\Edit\SaveController;
use Windwalker\Model\Exception\ValidateFailException;
use Windwalker\Data\Data;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Table\Table;
use Schedule\Table\Collection as TableCollection;
use Schedule\Helper\ScheduleHelper;

/**
 * Class ScheduleControllerRxresidentEditSave
 */
class ScheduleControllerRxresidentEditSave extends SaveController
{
	/**
	 * Property taskMapper.
	 *
	 * @var  DataMapper
	 */
	protected $taskMapper;

	/**
	 * Property scheduleMapper.
	 *
	 * @var  DataMapper
	 */
	protected $scheduleMapper;

	/**
	 * Property taskModel.
	 *
	 * @var  ScheduleModelTask
	 */
	protected $taskModel;

	/**
	 * Property scheduleModel.
	 *
	 * @var  ScheduleModelSchedule
	 */
	protected $scheduleModel;

	/**
	 * Property taskState.
	 *
	 * @var  JRegistry
	 */
	protected $taskState;

	/**
	 * Property scheduleState.
	 *
	 * @var  JRegistry
	 */
	protected $scheduleState;

	/**
	 * Property useTransaction.
	 *
	 * @var  bool
	 */
	protected $useTransaction = true;

	/**
	 * preSaveHook
	 *
	 * @throws  Windwalker\Model\Exception\ValidateFailException
	 * @return  void
	 */
	protected function preSaveHook()
	{
		if (! isset($this->data['items']))
		{
			$item = $this->data;

			$this->data['items'] = array();

			if (! empty($item['customer_id']))
			{
				$this->data['items'][$item['id']] = $item;
			}
		}

		if (empty($this->data['items']))
		{
			throw new ValidateFailException(array('empty items'));
		}

		foreach ($this->data['items'] as &$item)
		{
			$item['institute_id'] = $this->data['institute_id'];
			$item['floor'] = $this->data['floor'];
		}

		$this->createCustomers();
	}

	/**
	 * Save new customers
	 *
	 * @return  void
	 *
	 * @throws \Windwalker\Model\Exception\ValidateFailException
	 */
	protected function createCustomers()
	{
		$newCustomerIds = array();

		// Filter duplicated new customers
		foreach ($this->data['items'] as &$item)
		{
			$item['customer_id'] = trim($item['customer_id']);

			if (empty($item['customer_id']))
			{
				throw new ValidateFailException(array('客戶姓名未填寫!'));
			}

			if (! is_numeric($item['customer_id']))
			{
				$newCustomerIds[$item['customer_id']] = 0;
			}
		}

		foreach ($this->data['items'] as &$item)
		{
			$customerId = $item['customer_id'];

			if (! is_numeric($item['customer_id']) && 0 === $newCustomerIds[$customerId])
			{
				$customer = array(
					'name' => $item['customer_id'],
					'type' => 'resident',
					'institute_id' => $item['institute_id'],
				);

				/** @var ScheduleModelCustomer $customerModel */
				$customerModel = $this->getModel('Customer', '', array('ignore_request' => true));

				// Create new customer
				$customerModel->save($customer);

				$customer['id'] = $customerModel->getState()->get('customer.id');
				$newCustomerIds[$customerId] = $customer['id'];
			}
			else
			{
				$customerMapper = new DataMapper(Table::CUSTOMERS);

				if (isset($newCustomerIds[$customerId]) && $newCustomerIds[$customerId] > 0)
				{
					$customerId = $newCustomerIds[$customerId];
				}

				$customer = $customerMapper->findOne($customerId);
			}

			$item['customer_id'] = $customer['id'];
			$item['customer_name'] = $customer['name'];
		}
	}

	/**
	 * postSaveHook
	 *
	 * @param Windwalker\Model\CrudModel $model
	 * @param array                      $validDataSet
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validDataSet)
	{
		if (count($validDataSet) <= 0)
		{
			return;
		}

		$this->taskMapper = new DataMapper(Table::TASKS);
		$this->scheduleMapper = new DataMapper(Table::SCHEDULES);

		$this->taskModel = $this->getModel('Task', '', array('ignore_request' => true));
		$this->scheduleModel = $this->getModel('Schedule', '', array('ignore_request' => true));

		$this->taskState = $this->taskModel->getState();
		$this->scheduleState = $this->scheduleModel->getState();

		$customerIds = [];

		foreach ($validDataSet as $rx)
		{
			/** @var ScheduleTableInstitute $instituteTable */
			$instituteTable = TableCollection::loadTable('Institute', $rx['institute_id']);
			$deliverNths = array_flip($rx['deliver_nths']);
			$schedules = $this->scheduleMapper->find(array('rx_id' => $rx['id']));

			// The sender info is being done inside the prepareTable scheduleModel
			$this->scheduleState->set('sender_id', $instituteTable->sender_id);

			// Update exists schedule
			foreach ($schedules as $schedule)
			{
				/*
				 * Check if the "schedule deliver_nth" is in the "input deliver_nths list"
				 * If not exists, delete schedule data.
				 * Else, update exists schedule data.
				 */
				if (! isset($deliverNths[$schedule->deliver_nth]))
				{
					$this->scheduleMapper->delete($schedule->id);
				}
				else
				{
					$this->updateSchedule($schedule, $rx, $instituteTable);
				}

				unset($deliverNths[$schedule->deliver_nth]);
			}

			// Create new schedules
			foreach ($deliverNths as $nth => $tmp)
			{
				$this->createSchedule($nth, $rx, $instituteTable);
			}

			$customerIds[] = $rx['customer_id'];
		}

		/** @var ScheduleModelCustomer $customerModel */
		$customerModel = $this->getModel('Customer', '', array('ignore_request' => true));

		$customerModel->setCustomerState(1, $customerIds);
	}

	/**
	 * updateSchedule
	 *
	 * @param   Data                    $schedule        Exists schedule data
	 * @param   array                   $rx              Prescription data
	 * @param   ScheduleTableInstitute  $instituteTable  Institute table instance
	 *
	 * @return  void
	 */
	protected function updateSchedule($schedule, $rx, $instituteTable)
	{
		$sendDate = ScheduleHelper::calculateSendDate(
			$schedule->deliver_nth,
			$rx['see_dr_date'],
			$rx['period'],
			$instituteTable->delivery_weekday
		);

		$task = $this->taskMapper->findOne(
			array(
				'date' => $sendDate->format('Y-m-d', true),
				'sender' => $instituteTable->sender_id,
			)
		);

		if (empty($task->id))
		{
			$task = $this->createTaskData($sendDate, $instituteTable);
		}

		$newSchedule = $this->getScheduleData($rx, $schedule->deliver_nth, $task['id'], $sendDate);
		$newSchedule = array_merge((array) $schedule, $newSchedule);
		$newSchedule['weekday'] = $instituteTable->delivery_weekday;

		$this->scheduleModel->save($newSchedule);
	}

	/**
	 * createSchedule
	 *
	 * @param   string                  $nth             Nth of schedules
	 * @param   array                   $rx              Prescription data
	 * @param   ScheduleTableInstitute  $instituteTable  Institute table instance
	 *
	 * @return  void
	 */
	protected function createSchedule($nth, $rx, $instituteTable)
	{
		$sendDate = ScheduleHelper::calculateSendDate(
			$nth,
			$rx['see_dr_date'],
			$rx['period'],
			$instituteTable->delivery_weekday
		);

		$task = $this->taskMapper->findOne(
			array(
				'date' => $sendDate->format('Y-m-d', true),
				'sender' => $instituteTable->sender_id,
			)
		);

		if (empty($task->id))
		{
			$task = $this->createTaskData($sendDate, $instituteTable);
		}

		$schedule = $this->getScheduleData($rx, $nth, $task['id'], $sendDate);
		$schedule['weekday'] = $instituteTable->delivery_weekday;

		$this->scheduleState->set('schedule.id', 0);
		$this->scheduleModel->save($schedule);
	}

	/**
	 * createTaskData
	 *
	 * @param   JDate                   $sendDate
	 * @param   ScheduleTableInstitute  $instituteTable
	 *
	 * @return  Data
	 */
	protected function createTaskData(JDate $sendDate, $instituteTable)
	{
		$task = array(
			'status' => 0,
			'sender' => $instituteTable->sender_id,
			'sender_name' => $instituteTable->sender_name,
			'date' => $sendDate->toSql(true),
		);

		$this->taskState->set('task.id', 0);
		$this->taskModel->save($task);

		$task['id'] = $this->taskState->get('task.id');

		return new Data($task);
	}

	/**
	 * doSave
	 *
	 * @throws \Exception
	 * @return array
	 */
	protected function doSave()
	{
		// Access check.
		if (!$this->allowSave($this->data, $this->key))
		{
			throw new \Exception(\JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
		}

		$validDataSet = array();

		// Attempt to save the data.
		try
		{
			foreach ($this->data['items'] as $item)
			{
				$validDataSet[] = $this->saveItem($item);
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

		return $validDataSet;
	}

	/**
	 * saveAll
	 *
	 * @param   array  $data
	 *
	 * @throws  Exception
	 * @return  array
	 */
	private function saveItem(array $data)
	{
		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $this->model->getForm($data, false);

		// Test whether the data is valid.
		$validData = $this->model->validate($form, $data);

		if (!isset($validData['tags']))
		{
			$validData['tags'] = null;
		}

		$this->model->save($validData);

		$state = $this->model->getState();

		$validData['id'] = $state->get('rxresident.id');
		$validData['customer_name'] = $data['customer_name'];

		$state->set('rxresident.id', 0);
		$state->set('rxresident.new', false);

		return $validData;
	}

	/**
	 * getScheduleData
	 *
	 * @param   array   $rx        Prescription data
	 * @param   string  $nth       Deliver order
	 * @param   array   $taskId    Task id
	 * @param   JDate   $sendDate  Send date
	 *
	 * @return  array
	 */
	private function getScheduleData($rx, $nth, $taskId, $sendDate)
	{
		$drugEmptyDate = DateHelper::getDate($rx['see_dr_date']);
		$number = (int) substr($nth, 0, 1);
		$modify = sprintf('+%s day', ($number - 1) * $rx['period']);

		$drugEmptyDate->modify($modify);

		return array(
			'task_id' => $taskId,
			'customer_id' => $rx['customer_id'],
			'customer_name' => $rx['customer_name'],
			'institute_id' => $rx['institute_id'],
			'rx_id' => $rx['id'],
			'type' => 'resident',
			'date' => $sendDate->toSql(true),
			'deliver_nth' => $nth,
			'drug_empty_date' => $drugEmptyDate->toSql(true),
			'session' => 'daytime',
			'status' => 'scheduled',
			'sorted' => 0,
		);
	}
}
