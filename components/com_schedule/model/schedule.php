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
	 * Get schedule form object to perform validation
	 *
	 * @return  \JForm
	 */
	public function getScheduleForm()
	{
		$config = array(
			'control'   => 'jform',
			'load_data' => false,
		);

		$formName = 'prescription_schedule';

		return $this->loadForm($this->option . '.' . $formName . '.form', $formName, $config);
	}
}
