<?php

use Windwalker\Controller\Admin\AbstractRedirectController;

/**
 * Class ScheduleControllerRxResidentEditList
 */
class ScheduleControllerRxResidentEditList extends AbstractRedirectController
{
	/**
	 * doExecute
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		$id = (int) $this->input->get('id');
		$cid = $this->input->get('cid', array(), 'ARRAY');

		if ($id > 0 && empty($cid))
		{
			$cid = array($id);
		}

		$cid = array_unique($cid);

		$query = array(
			'option' => $this->option,
			'view' => strtolower($this->getName()),
			'layout' => 'edit_list',
			'cid' => $cid,
		);

		$url = JRoute::_('index.php?' . http_build_query($query), false);

		$this->redirect($url);

		return true;
	}
}
