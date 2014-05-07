<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class DeliveryController
 *
 * @since 1.0
 */
class ScheduleControllerTasksStateDelivery extends \Windwalker\Controller\State\AbstractUpdateStateController
{
	/**
	 * Property stateData.
	 *
	 * @var string
	 */
	protected $stateData = array(
		'status' => 1
	);

	/**
	 * Property actionText.
	 *
	 * @var string
	 */
	protected $actionText = 'DELIVERED';
}
