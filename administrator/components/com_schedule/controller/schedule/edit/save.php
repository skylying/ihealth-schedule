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

		$state->set('form.type', $this->input->get('form_type', 'schedule_institute'));

		parent::preSaveHook();
	}
}
