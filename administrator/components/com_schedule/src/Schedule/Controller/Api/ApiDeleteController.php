<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Controller\Api;

use Windwalker\Controller\State\DeleteController;

/**
 * Class ApiDeleteController
 *
 * @since 1.0
 */
class ApiDeleteController extends DeleteController
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

	/**
	 * Check session token or die.
	 *
	 * @return void
	 */
	protected function checkToken()
	{
	}

	/**
	 * Method to check delete access.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowDelete($data = array(), $key = 'id')
	{
		return true;
	}
}
