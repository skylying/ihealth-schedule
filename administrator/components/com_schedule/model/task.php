<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Model\TaskModel;
use Schedule\Table\Table;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelTask
 *
 * @since 1.0
 */
class ScheduleModelTask extends TaskModel
{
	/**
	 * updateScheduleAsDelivered
	 *
	 * @param   int  $taskId  Task Id
	 *
	 * @return  int
	 */
	public function updateScheduleAsDelivered($taskId)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->clear()
			->update(Table::SCHEDULES)
			->set('status = "delivered"')
			->set('notify = 0')
			->where('task_id = ' . $taskId)
			->where('status = "scheduled"');

		$db->setQuery($query)->execute();

		return $db->getAffectedRows();
	}
}
