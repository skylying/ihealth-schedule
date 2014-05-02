<?php

use Windwalker\Controller\Edit\AddController;

/**
 * Class ScheduleControllerRxresidentEditAdd
 */
class ScheduleControllerRxresidentEditAdd extends AddController
{
	/**
	 * redirectToItem
	 *
	 * @param   string  $recordId
	 * @param   string  $urlVar
	 * @param   string  $msg
	 * @param   string  $type
	 *
	 * @return  void
	 */
	public function redirectToItem($recordId = null, $urlVar = 'id', $msg = null, $type = 'message')
	{
		$this->input->set('layout', 'edit_list');

		parent::redirectToItem($recordId, $urlVar, $msg, $type);
	}
}
