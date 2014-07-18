<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\State\AbstractUpdateStateController;

/**
 * Class DeliveryController
 *
 * @since 1.0
 */
class ScheduleControllerTasksStateDelivery extends AbstractUpdateStateController
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

	/**
	 * postUpdateHook
	 *
	 * @param ScheduleModelTask $model
	 *
	 * @return void
	 */
	protected function postUpdateHook($model)
	{
		// Update schedule status to delivered
		$pks = $this->cid;

		foreach ($pks as $taskId)
		{
			$model->updateScheduleAsDelivered($taskId);
		}

		parent::postUpdateHook($model);
	}
}
