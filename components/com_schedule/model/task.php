<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Model\TaskModel;

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
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	public function prepareTable(\JTable $table)
	{
		parent::prepareTable($table);
	}
}
