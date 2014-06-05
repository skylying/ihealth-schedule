<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Model\Customer;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelCustomer
 *
 * @since 1.0
 */
class ScheduleModelCustomer extends Customer
{
	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	public function prepareTable(JTable $table)
	{
		parent::prepareTable($table);

		// TODO: Use the input format in front-end and back-end
		// TODO: Move json_encode convert process to \Schedule\Model\Customer::prepareTable()

		if (is_array($table->tel_office))
		{
			$table->tel_office = json_encode(array_values($table->tel_office));
		}

		if (is_array($table->tel_home))
		{
			$table->tel_home = json_encode(array_values($table->tel_home));
		}

		if (is_array($table->mobile))
		{
			$table->mobile = json_encode(array_values($table->mobile));
		}
	}
}
