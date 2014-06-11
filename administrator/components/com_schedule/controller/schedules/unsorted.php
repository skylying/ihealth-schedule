<?php

use Windwalker\Controller\State\AbstractUpdateStateController;

/**
 * Class ScheduleControllerSchedulesUnsorted
 *
 * @since 1.0
 */
class ScheduleControllerSchedulesUnsorted extends AbstractUpdateStateController
{
	/**
	 * The data fields to update.
	 *
	 * @var string
	 */
	protected $stateData = array(
		'sorted' => 0
	);

	/**
	 * Action text for translate.
	 *
	 * @var string
	 */
	protected $actionText = 'UNSORTED';

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
}
