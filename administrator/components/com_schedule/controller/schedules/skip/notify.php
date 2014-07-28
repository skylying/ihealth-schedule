<?php

use Windwalker\Controller\State\AbstractUpdateStateController;

/**
 * ScheduleControllerSchedulesSkipNotify
 *
 * @since 1.0
 */
class ScheduleControllerSchedulesSkipNotify extends AbstractUpdateStateController
{
	/**
	 * The data fields to update.
	 *
	 * @var string
	 */
	protected $stateData = array(
		'notify' => '0',
	);

	/**
	 * Action text for translate.
	 *
	 * @var string
	 */
	protected $actionText = 'NOTIFY';

	/**
	 * Are we allow return?
	 *
	 * @var  boolean
	 */
	protected $allowReturn = true;

	/**
	 * Use DB transaction or not.
	 *
	 * @var  boolean
	 */
	protected $useTransaction = true;

	/**
	 * Prepare execute hook.
	 *
	 * @throws \LogicException
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		$cid = $this->input->getVar('notify_schedule_cid', '');

		if (!empty($cid))
		{
			$this->cid = explode(',', $cid);
		}
	}
}
