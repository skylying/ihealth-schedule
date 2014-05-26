<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Schedule\Controller\Api\ApiDeleteController;

/**
 * Class ScheduleControllerMemberStateDelete
 *
 * @since 1.0
 */
class ScheduleControllerMemberStateDelete extends ApiDeleteController
{
	/**
	 * prepareExecute
	 *
	 * @return  void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		$id = $this->input->get('id');

		if ($id)
		{
			$this->cid = array($id);
		}
	}

	/**
	 * Pose execute hook.
	 *
	 * @param   mixed $return Executed return value.
	 *
	 * @throws  \Exception
	 * @return  mixed
	 */
	protected function postExecute($return = null)
	{
		if (! $return)
		{
			throw new \Exception('Delete fail.');
		}

		$state = $this->model->getState();

		$this->setMessage('Delete Success.');

		return $this->fetch('Schedule', $this->name . '.display', array('id' => $state->get($this->name . '.id')));
	}
}
