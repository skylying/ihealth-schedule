<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\State\AbstractUpdateStateController;

/**
 * Class UndeliveryController
 *
 * @since 1.0
 */
class ScheduleControllerTasksStateUndelivery extends AbstractUpdateStateController
{
	/**
	 * Property stateData.
	 *
	 * @var string
	 */
	protected $stateData = array(
		'status' => 0
	);

	/**
	 * Property actionText.
	 *
	 * @var string
	 */
	protected $actionText = 'UNDELIVERED';
}
