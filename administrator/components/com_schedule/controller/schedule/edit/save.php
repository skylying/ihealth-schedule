<?php

use Windwalker\Controller\Edit\SaveController;
use Schedule\Table\Table;
use Windwalker\Data\Data;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Helper\DateHelper;

/**
 * Class SaveController
 *
 * @since 1.0
 */
class ScheduleControllerScheduleEditSave extends SaveController
{
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

		// Put xml bi-type into data type
		if (isset($this->data['institute_type']))
		{
			$this->data['type'] = $this->data['institute_type'];
		}
		elseif (isset($this->data['individual_type']))
		{
			$this->data['type'] = $this->data['individual_type'];
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
