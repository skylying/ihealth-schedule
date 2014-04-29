<?php

use Windwalker\Controller\Admin\AbstractRedirectController;

/**
 * Class ScheduleControllerRxResidentEditAdd
 */
class ScheduleControllerRxResidentEditAdd extends AbstractRedirectController
{
	/**
	 * doExecute
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		$query = array(
			'option' => $this->option,
			'view' => strtolower($this->getName()),
			'layout' => 'edit_list',
		);

		$url = JRoute::_('index.php?' . http_build_query($query), false);

		$this->redirect($url);

		return true;
	}
}
