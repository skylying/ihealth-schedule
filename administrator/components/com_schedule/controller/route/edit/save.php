<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Model\Exception\ValidateFailException;
use Schedule\Table\Collection as TableCollection;
use Schedule\Table\Table;
use Schedule\Helper\ScheduleHelper;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;

/**
 * Class ScheduleControllerRouteEditSave
 */
class ScheduleControllerRouteEditSave extends SaveController
{
	/**
	 * Property useTransaction.
	 *
	 * @var  bool
	 */
	protected $useTransaction = true;

	/**
	 * Property scheduleModel.
	 *
	 * @var  ScheduleModelSchedule
	 */
	protected $scheduleModel;

	/**
	 * Property taskMapper.
	 *
	 * @var  DataMapper
	 */
	protected $taskMapper;

	/**
	 * Property taskModel.
	 *
	 * @var  ScheduleModelTask
	 */
	protected $taskModel;

	/**
	 * Property taskState.
	 *
	 * @var  JRegistry
	 */
	protected $taskState;

	/**
	 * preSaveHook
	 *
	 * @return  void
	 */
	protected function preSaveHook()
	{
		parent::preSaveHook();

		// Get data input ("sender_id" and "weekday")
		$this->data = $this->input->get('data', array(), 'ARRAY');
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

		$cid = $this->input->get('cid', array(), 'ARRAY');

		$validDataSet = array();

		// Attempt to save the data.
		try
		{
			foreach ($cid as $id)
			{
				$validDataSet[] = $this->saveItem($id);
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
	 * Method that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   \Windwalker\Model\CrudModel  $model         The data model object.
	 * @param   array                        $validDataSet  The validated data.
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validDataSet)
	{
		$this->scheduleModel = $this->getModel('Schedule', '', array('ignore_request' => true));
		$this->taskModel = $this->getModel('Task', '', array('ignore_request' => true));
		$this->taskState = $this->taskModel->getState();
		$this->taskMapper = new DataMapper(Table::TASKS);

		// Fix task "edit.apply" redirect to edit page without id
		if (count($validDataSet) === 1)
		{
			$this->model->getState()->set('route.id', $validDataSet[0]['id']);
		}

		$modelInstitute = $this->getModel('Institute');

		foreach ($validDataSet as $data)
		{
			$route = $this->model->getItem($data['id']);

			// Update institute "sender_id", "sender_name" and "delivery_weekday"
			if ('institute' === $route->type)
			{
				$updateData = array(
					'id' => $route->institute_id,
					'sender_id' => $route->sender_id,
					'sender_name' => $route->sender_name,
					'delivery_weekday' => $route->weekday,
				);

				$modelInstitute->save($updateData);
			}

			$this->updateSchedules($route);
		}
	}

	/**
	 * foo
	 *
	 * @param  \stdClass  $route  Route data
	 *
	 * @return  void
	 */
	private function updateSchedules($route)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$select = [
			'schedule.id', 'schedule.task_id', 'rx.see_dr_date', 'rx.`period`',
			'schedule.deliver_nth', 'schedule.`weekday`', 'schedule.`date`'
		];

		// 取得需要修改日期的排程
		$query->clear()
			->select($select)
			->from(Table::SCHEDULES . ' AS schedule')
			->leftJoin(Table::PRESCRIPTIONS . ' AS rx ON schedule.rx_id=rx.id')
			->where('schedule.route_id=' . $route->id)
			->where('schedule.`weekday`<>' . $db->q($route->weekday))
			->where('schedule.`status` NOT IN ("deleted", "cancel_only", "delivered")');

		$schedules = $db->setQuery($query)->loadAssocList();
		$tasks = [];

		foreach ($schedules as $index => $schedule)
		{
			$sendDate = ScheduleHelper::calculateSendDate(
				$schedule['deliver_nth'],
				$schedule['see_dr_date'],
				$schedule['period'],
				$route->weekday
			);

			$taskKey = $sendDate->toSql(true) . $route->sender_id;

			if (empty($tasks[$taskKey]))
			{
				$task = $this->taskMapper->findOne(
					array(
						'date' => $sendDate->toSql(true),
						'sender' => $route->sender_id,
					)
				);

				if (empty($task->id))
				{
					$task = $this->createTask($sendDate, $route);
				}

				$tasks[$taskKey] = $task;
			}

			$schedule['date'] = $sendDate->toSql(true);
			$schedule['task_id'] = $tasks[$taskKey]->id;
			$schedule['sender_id'] = $route->sender_id;
			$schedule['sender_name'] = $route->sender_name;
			$schedule['weekday'] = $route->weekday;

			$this->scheduleModel->save($schedule);
		}
	}

	/**
	 * createTask
	 *
	 * @param   JDate      $sendDate
	 * @param   \stdClass  $route
	 *
	 * @return  Data
	 */
	private function createTask(JDate $sendDate, $route)
	{
		$task = array(
			'status' => 0,
			'sender' => $route->sender_id,
			'sender_name' => $route->sender_name,
			'date' => $sendDate->toSql(true),
		);

		$this->taskState->set('task.id', 0);
		$this->taskModel->save($task);

		$task['id'] = $this->taskState->get('task.id');

		return new Data($task);
	}

	/**
	 * saveAll
	 *
	 * @param   int  $id
	 *
	 * @return  array
	 */
	private function saveItem($id)
	{
		$data = $this->data;

		if (! empty($id))
		{
			$data['id'] = $id;
		}
		else
		{
			if (! empty($data['institute_id']))
			{
				// When institute_id exists, update route with exists route id
				$routeTable = TableCollection::loadTable('Route', ['institute_id' => $data['institute_id']]);

				if (! empty($routeTable->id))
				{
					$data['id'] = $routeTable->id;
				}
			}
		}

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

		$validData['id'] = $state->get('route.id');

		$state->set('route.id', 0);
		$state->set('route.new', false);

		return $validData;
	}
}
