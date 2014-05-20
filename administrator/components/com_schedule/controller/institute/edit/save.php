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
	 *
	 * @param \Windwalker\Model\CrudModel $model
	 * @param array                       $validateData
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validateData)
	{
		$data = $validateData;
		$state = $model->getState();

		// Get current institute id
		$instituteId = $state->get('institute.id');

		// Prepare all data mapper we need
		$routeMapper     = new DataMapper(Table::ROUTES);
		$cityMapper      = new DataMapper(Table::CITIES);
		$areaMapper      = new DataMapper(Table::AREAS);
		$senderMapper    = new DataMapper(Table::SENDERS);
		$instituteMapper = new DataMapper(Table::INSTITUTES);

		// Get city title
		$cityData  = $cityMapper->findOne(array("id" => $data['city']));
		$cityTitle = $cityData->title;

		// Get area title
		$areaData = $areaMapper->findOne(array("id" => $data['area']));
		$areaTitle = $areaData->title;

		// Get sender name
		$senderData = $senderMapper->findOne(array("id" => $data['sender_id']));
		$senderName = $senderData->name;

		// Inject all route data
		$routeData = array(
			'id'           => empty($data['route_id']) ? 0 : $data['route_id'],
			'sender_id'    => $data['sender_id'],
			'type'         => 'institute',
			'institute_id' => $instituteId,
			'city'         => $data['city'],
			'city_title'   => $cityTitle,
			'area'         => $data['area'],
			'area_title'   => $areaTitle,
			'weekday'      => $data['delivery_weekday'],
			'sender_name'  => $senderName,
		);

		// No route id, do create. Has route id, do update.
		if ($data['route_id'] == 0)
		{
			$routeMapper->createOne(new Data($routeData));
		}
		else
		{
			$routeMapper->updateOne(new Data($routeData));
		}

		// Update route id in institute table
		$routePostData = $routeMapper->findOne(array("institute_id" => $instituteId));
		$routeId = $routePostData->id;

		$instituteMapper->updateOne(new Data(array('id' => $instituteId, 'route_id' => $routeId)));
	}
}
