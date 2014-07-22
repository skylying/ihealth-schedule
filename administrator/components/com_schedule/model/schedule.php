<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Model\ScheduleModel;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelSchedule
 *
 * @since 1.0
 */
class ScheduleModelSchedule extends ScheduleModel
{
	/**
	 * Override prepare table to save last person who edit "sorted" field
	 *
	 * @param  \JTable $table
	 *
	 * @return  void
	 */
	public function prepareTable(\JTable $table)
	{
		parent::prepareTable($table);

		$user = $this->getContainer()->get('user');

		$sortedList = JFactory::getApplication()->getUserState('drugdetail.sorted.list');

		if (isset($sortedList[$table->id])
			&& $sortedList[$table->id] != $table->sorted)
		{
			$table->modified_by = $user->get('id');
		}
		else
		{
			// Do not update modified_by if sorted was not changed
			unset($table->modified_by);
		}
	}
}
