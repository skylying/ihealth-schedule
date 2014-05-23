<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Model\Exception\ValidateFailException;
use Windwalker\Data\Data;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Table\Table;
use Schedule\Table\Collection as TableCollection;
use Schedule\Helper\ScheduleHelper;

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
			foreach ($cid as $value)
			{
				$validDataSet[] = $this->saveItem($value);
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

		// Update institute "sender_id" and "delivery_weekday"
		foreach ($validDataSet as $data)
		{
			if (! empty($data['type']) && 'institute' === $data['type'])
			{
				$updateData = array(
					'id' => $data['institute_id'],
				);

				// Prevent not set data
				if (isset($data['sender_id']))
				{
					$updateData['sender_id'] = $data['sender_id'];
				}

				// Prevent not set data
				if (isset($data['weekday']))
				{
					$updateData['delivery_weekday'] = $data['weekday'];
				}

				$modelInstitute->save($updateData);
			}
		}
	}

	/**
	 * saveAll
	 *
	 * @param  array $singleCid
	 *
	 * @return array
	 */
	private function saveItem($singleCid)
	{
		// Get sender id and weekday from post input value
		$data = $this->input->get('routeupdater', array(), 'ARRAY');

		// If no sender_id or weekday, unset the empty value
		foreach ($data as $key => $value)
		{
			if (empty($value))
			{
				unset($data[$key]);
			}
		}

		// Get route type, institute_id
		$decodedData = (array) json_decode($singleCid);

		// Combine all route information
		$data = $decodedData + $data;

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
