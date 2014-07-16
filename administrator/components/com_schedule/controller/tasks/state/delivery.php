<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\State\AbstractUpdateStateController;
use Schedule\Table\Table;

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
	 * @param \Windwalker\Model\Model $model
	 *
	 * @return void
	 */
	protected function postUpdateHook($model)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		// Update schedule status to delivered
		$pks = $this->cid;

		foreach ($pks as $id)
		{
			$query->clear()
				->update(Table::SCHEDULES)
				->set('status = "delivered"')
				->where('task_id = ' . $id)
				->where('status = "scheduled"');

			$db->setQuery($query)->execute();
		}

		parent::postUpdateHook($model);
	}
}
