<?php

use Windwalker\Controller\Edit\SaveController;

/**
 * Class ScheduleControllerHospitalEditSave
 *
 * @since 1.0
 */
class ScheduleControllerHospitalEditSave extends SaveController
{
	/**
	 * Method that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   \Windwalker\Model\CrudModel  $model      The data model object.
	 * @param   array                        $validData  The validated data.
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validData)
	{
		$hospitalId = $model->getState()->get('hospital.id');
		$imageModel = $this->getModel('Image');

		foreach (['image1', 'image2'] as $key)
		{
			if ($this->data[$key] > 0)
			{
				$image = array(
					'id' => $this->data[$key],
					'hospital_id' => $hospitalId,
				);

				$imageModel->save($image);
			}
		}

		parent::preSaveHook();
	}
}
