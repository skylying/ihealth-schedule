<?php

use Windwalker\Controller\Edit\SaveController;
use Schedule\Helper\ScheduleHelper;
use Schedule\Table\Table;
use Schedule\Table\Collection as TableCollection;
use Windwalker\Data\Data;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Helper\MailHelper;

/**
 * Class SaveController
 *
 * @since 1.0
 */
class ScheduleControllerScheduleEditSave extends SaveController
{
	/**
	 * Property sendNotifyMail.
	 *
	 * @var  bool
	 */
	protected $sendNotifyMail = false;

	/**
	 * preSaveHook
	 *
	 * @return  void
	 */
	protected function preSaveHook()
	{
		$state = $this->model->getState();

		$state->set('sender_id', $this->data['sender_id']);
		$state->set('form.type', $this->input->get('form_type', 'schedule_institute'));

		if (!empty($this->data['id']) && $this->data['id'] > 0)
		{
			$oldScheduleTable = TableCollection::loadTable('Schedule', $this->data['id']);

			if (! empty($oldScheduleTable->id)
				&& ScheduleHelper::checkScheduleChanged($oldScheduleTable->getProperties(), $this->data))
			{
				$this->sendNotifyMail = true;
			}
		}

		$task = (new DataMapper(Table::TASKS))->findOne(
			array(
				'date' => $this->data['date'],
				'sender' => $this->data['sender_id'],
			)
		);

		if (empty($task->id))
		{
			$task = $this->createTaskData();
		}

		$this->data['task_id'] = $task->id;

		parent::preSaveHook();
	}

	/**
	 * Pose execute hook.
	 *
	 * @param   mixed  $return  Executed return value.
	 *
	 * @return  mixed
	 */
	protected function postExecute($return = null)
	{
		if ('component' !== $this->input->get('tmpl'))
		{
			return parent::postExecute($return);
		}

		// Clear the record id and data from the session.
		$this->releaseEditId($this->context, $this->recordId);

		if (false !== $return)
		{
			$this->app->setUserState($this->context . '.data', null);
		}

		$js = <<<JAVASCRIPT
<script>
parent.closeModal("#modal-add-new-item");
parent.location.reload(true);
</script>
JAVASCRIPT;

		jexit($js);

		return $return;
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
		parent::postSaveHook($model, $validData);

		if ($this->sendNotifyMail)
		{
			$oldScheduleTable = TableCollection::loadTable('Schedule', $this->data['id']);

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
	 * createTaskData
	 *
	 * @return  Data
	 */
	protected function createTaskData()
	{
		$task = array(
			'status' => 0,
			'sender' => $this->data['sender_id'],
			'date' => $this->data['date'],
		);

		/** @var ScheduleModelTask $taskModel */
		$taskModel = $this->getModel('Task');

		$taskModel->save($task);

		$task['id'] = $taskModel->getState()->get('task.id');

		return new Data($task);
	}
}
