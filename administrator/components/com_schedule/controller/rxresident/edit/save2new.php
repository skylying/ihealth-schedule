<?php

use \Windwalker\Controller\Edit\Save2newController;

/**
 * Class ScheduleControllerRxresidentEditSave2New
 */
class ScheduleControllerRxresidentEditSave2New extends Save2newController
{
	/**
	 * Prepare execute hook.
	 *
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		$this->context = $this->option . '.item.edit.save';
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
		// Clear the record id and data from the session.
		$this->releaseEditId($this->context, $this->recordId);

		$this->app->setUserState($this->context . '.return', $return);

		if (true === $return)
		{
			$this->app->setUserState($this->context . '.data', null);
		}

		// Redirect back to the edit screen.
		$this->input->set('layout', 'edit_list');

		$this->redirectToItem();

		return $return;
	}
}
