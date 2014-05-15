<?php

use Windwalker\Controller\Edit\SaveController;

/**
 * Class ScheduleControllerRxresidentEditSave
 */
class ScheduleControllerHolidayEditSave extends SaveController
{
	/**
	 * Property databaseField.
	 *
	 * @var  array
	 */
	protected $databaseField = array(
		'id',
		'year',
		'month',
		'title',
		'weekday',
		'date',
		'state'
	);

	/**
	 * doSave
	 *
	 * @return  array|void
	 */
	protected function doSave()
	{
		$formatedData = $this->getFormatedData($this->data['date']);

		// Attemp to save each data
		foreach ($formatedData as $value)
		{
			$this->saveItem($value);
		}
	}

	/**
	 * saveItem
	 *
	 * @param array $data
	 *
	 * @return  void
	 */
	private function saveItem(array $data)
	{
		$this->model->save($data);

		$state = $this->model->getState();

		$state->set('holiday.id', 0);
		$state->set('holiday.new', false);
	}

	/**
	 * getFormatedData
	 *
	 * @param array $rawData
	 *
	 * @return  array
	 */
	protected function getFormatedData(array $rawData)
	{
		$formatedData = array();

		foreach ($rawData as $key => $value)
		{
			// Convert json string
			$decodedData = json_decode($value, true);

			$tmp = array();

			foreach ($decodedData as $field => $fieldValue)
			{
				if ($field == 'holidayId')
				{
					$tmp['id'] = $fieldValue;

					continue;
				}

				$tmp[$field] = $fieldValue;
			}

			$formatedData[] = $tmp;
		}

		return $formatedData;
	}
}
