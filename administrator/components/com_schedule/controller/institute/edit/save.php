<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Table;

/**
 * Class SaveController
 *
 * @since 1.0
 */
class ScheduleControllerInstituteEditSave extends SaveController
{

	/**
	 * Post-save route table, update institute table
	 * Mind that city_title, area_title, sender_name will be prepared in route prepareTable()
	 *
	 * @param \Windwalker\Model\CrudModel $model
	 * @param array                       $validateData
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validateData)
	{
		$data  = $validateData;
		$state = $model->getState();

		// Get route model
		$routeModel = $this->getModel('Route');

		// Get institute Mapper to update route id later
		$instituteMapper = new DataMapper(Table::INSTITUTES);

		// Get current institute id
		$instituteId = $state->get('institute.id');

		// Inject all route data
		$routeData = array(
			'id'           => empty($data['route_id']) ? 0 : $data['route_id'],
			'sender_id'    => $data['sender_id'],
			'type'         => 'institute',
			'institute_id' => $instituteId,
			'city'         => $data['city'],
			'area'         => $data['area'],
			'weekday'      => $data['delivery_weekday'],
		);

		// Attemp to save route data
		$routeModel->save($routeData);

		// Get route id
		$routeId = $routeModel->getState()->get('route.id');

		// Update the newly created route id in institute table
		$instituteMapper->updateOne(new Data(array('id' => $instituteId, 'route_id' => $routeId)));
	}
}
