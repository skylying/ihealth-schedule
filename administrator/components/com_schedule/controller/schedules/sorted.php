<?php

use Windwalker\Controller\State\AbstractUpdateStateController;

/**
 * Class ScheduleControllerSchedulesSorted
 *
 * @since 1.0
 */
class ScheduleControllerSchedulesSorted extends AbstractUpdateStateController
{
	/**
	 * The data fields to update.
	 *
	 * @var string
	 */
	protected $stateData = array(
		'sorted' => 1
	);

	/**
	 * Action text for translate.
	 *
	 * @var string
	 */
	protected $actionText = 'SORTED';

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
