<?php

use Windwalker\Controller\Edit\SaveController;

/**
 * Class ScheduleControllerRxresidentEditSave
 */
class ScheduleControllerRxresidentEditSave extends SaveController
{
	/**
	 * preSaveHook
	 *
	 * @return  void
	 */
	protected function preSaveHook()
	{
		if (empty($this->data['item']['new']))
		{
			$this->data['item']['new'] = array();
		}

		if (empty($this->data['item']['old']))
		{
			$this->data['item']['old'] = array();
		}

		foreach ($this->data['item']['new'] as &$item)
		{
			$item['institute_id'] = $this->data['institute_id'];
			$item['floor'] = $this->data['floor'];
		}

		foreach ($this->data['item']['old'] as &$item)
		{
			$item['institute_id'] = $this->data['institute_id'];
			$item['floor'] = $this->data['floor'];
		}
	}

	/**
	 * postSaveHook
	 *
	 * @param Windwalker\Model\CrudModel $model
	 * @param array                      $validDataSet
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validDataSet)
	{
		/*
		 * TODO: save schedule
		 * TODO: save task
		 */
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

		$validDataSet = array();

		foreach ($this->data['item']['new'] as $hash => $item)
		{
			$validDataSet['new' . $hash] = $this->saveItem($item);
		}

		foreach ($this->data['item']['old'] as $hash => $item)
		{
			$validDataSet['old' . $hash] = $this->saveItem($item);
		}

		return $validDataSet;
	}

	/**
	 * saveAll
	 *
	 * @param   array  $data
	 *
	 * @throws  Exception
	 * @return  array
	 */
	private function saveItem(array $data)
	{
		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $this->model->getForm($data, false);

		// Test whether the data is valid.
		$validData = $this->model->validate($form, $data);

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
			$this->app->setUserState($this->context . '.data', $this->data);

			// Redirect back to the edit screen.
			throw new \Exception(\JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $e->getMessage()));
		}

		// TODO: 出現例外時，還原所有的 FORM

		$state = $this->model->getState();

		$validData['id'] = $state->get('rxresident.id');

		return $validData;
	}
}
