<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Model\Exception\ValidateFailException;
use Schedule\Table\Collection as TableCollection;

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
		}
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
