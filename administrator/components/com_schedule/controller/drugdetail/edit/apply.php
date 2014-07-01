<?php

/**
 * Class SaveController
 *
 * @since 1.0
 */
class ScheduleControllerDrugdetailEditApply extends ScheduleControllerDrugdetailEditSave
{
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
		$ids  = $this->data["senderIds"];
		$date = $this->data["date"];
		$url  = $this->getRedirectItemUrl();

		$urlValue = http_build_query(
			array(
				'layout' => 'edit',
				'date' => $date,
				'senderIds' => $ids
			)
		);

		$this->app->redirect($url . "&" . urldecode($urlValue));
	}
}
