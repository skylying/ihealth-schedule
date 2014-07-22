<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Controller\Api;

use Windwalker\Controller\Edit\SaveController;

/**
 * Save controller for API, do not check ACL.
 *
 * @since 1.0
 */
class ApiSaveController extends SaveController
{
	/**
	 * prepareExecute
	 *
	 * @return  void
	 */
	protected function prepareExecute()
	{
		// Do not redirect
		$this->input->set('redirect', false);

		parent::prepareExecute();

		$this->data = $_POST;
	}

	/**
	 * postExecute
	 *
	 * @param   boolean $return
	 *
	 * @throws  \Exception
	 * @return  mixed
	 */
	protected function postExecute($return = null)
	{
		if (! $return)
		{
			throw new \Exception('Save fail');
		}

		$this->input->set('layout', null);

		// Clear the record id and data from the session.
		$this->releaseEditId($this->context, $this->recordId);

		if (false !== $return)
		{
			$this->app->setUserState($this->context . '.data', null);
		}

		$state = $this->model->getState();

		return $this->fetch('Schedule', $this->name . '.display', array('id' => $state->get($this->name . '.id')));
	}

	/**
	 * Do the save action.
	 *
	 * @throws \Exception
	 * @return array Validated data.
	 */
	protected function doSave()
	{
		$key  = $this->key;

		// Access check.
		if (!$this->allowSave($this->data, $key))
		{
			throw new \Exception(\JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $this->model->getForm($this->data, false);

		// Test whether the data is valid.
		$validData = $this->model->validate($form, $this->data);

		if (!isset($validData['tags']))
		{
			$validData['tags'] = null;
		}

		// Attempt to save the data.
		try
		{
			$this->model->save($validData);
		}
		catch (\Exception $e)
		{
			// Save the data in the session.
			$this->app->setUserState($this->context . '.data', $validData);

			// Redirect back to the edit screen.
			throw $e;
		}

		return $validData;
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
	 * Method to check if you can save a new or existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function allowSave($data, $key = 'id')
	{
		return true;
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return true;
	}

	/**
	 * Check update access.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowUpdateState($data = array(), $key = 'id')
	{
		return true;
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
