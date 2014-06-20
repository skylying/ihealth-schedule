<?php

use Windwalker\Controller\Edit\SaveController;

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
parent.location.href = parent.location.href;
</script>
JAVASCRIPT;

		jexit($js);

		return $return;
	}
}
