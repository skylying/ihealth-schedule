<?php

use Windwalker\Controller\Edit\CancelController;

/**
 * Class SaveController
 *
 * @since 1.0
 */
class ScheduleControllerDrugdetailEditCancel extends CancelController
{
	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'schedules';

	/**
	 * prepareExecute
	 *
	 * @return  void
	 */
	protected function prepareExecute()
	{
		// Remove sorted list state
		JFactory::getApplication()->setUserState('drugdetail.sorted.list', null);

		parent::prepareExecute();
	}
}
