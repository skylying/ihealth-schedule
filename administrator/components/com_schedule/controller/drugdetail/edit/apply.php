<?php

/**
 * Class SaveController
 *
 * @since 1.0
 */
class ScheduleControllerDrugdetailEditApply extends ScheduleControllerDrugdetailEditSave
{
	/**
	 * postExecute
	 *
	 * @param null $return
	 *
	 * @return  mixed|void
	 */
	public function postExecute($return = null)
	{
		$printButtonValue = $this->input->get('save-and-print');

		if (false !== $return & $printButtonValue == '1')
		{
			$this->app->setUserState('save-and-print', $printButtonValue);
		}

		return parent::postExecute($return);
	}
	/**
	 * Redirect
	 *
	 * @param string $url
	 * @param null   $msg
	 * @param string $type
	 *
	 * @return  void
	 */
	public function redirect($url, $msg = null, $type = 'message')
	{
		$url  = $this->getRedirectItemUrl();

		$urlValue = http_build_query(
			array(
				'layout' => 'edit',
				'date_start' => $this->data['date_start'],
				'date_end' => $this->data['date_end'],
				'senderIds' => $this->data['senderIds'],
			)
		);

		$this->app->redirect($url . "&" . urldecode($urlValue));
	}
}
