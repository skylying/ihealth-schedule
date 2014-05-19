<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Model\AdminModel;
use Schedule\Table\Collection as TableCollection;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelRoute
 *
 * @since 1.0
 */
class ScheduleModelRoute extends AdminModel
{
	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'schedule';

	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option = 'com_schedule';

	/**
	 * Property textPrefix.
	 *
	 * @var string
	 */
	protected $textPrefix = 'COM_SCHEDULE';

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'route';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'route';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'routes';

	/**
	 * Method to set new item ordering as first or last.
	 *
	 * @param   JTable $table    Item table to save.
	 * @param   string $position 'first' or other are last.
	 *
	 * @return  void
	 */
	public function setOrderPosition($table, $position = 'last')
	{
		parent::setOrderPosition($table, $position);
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	protected function prepareTable(\JTable $table)
	{
		parent::prepareTable($table);

		$tableSender = TableCollection::loadTable('Sender', $table->sender_id);

		$table->sender_name = $tableSender->name;

		if ('customer' === $table->type)
		{
			$table->institute_id = 0;
		}
		elseif ('institute' === $table->type)
		{
			$tableInstitute = TableCollection::loadTable('Institute', $table->institute_id);

			$table->city       = $tableInstitute->city;
			$table->city_title = $tableInstitute->city_title;
			$table->area       = $tableInstitute->area;
			$table->area_title = $tableInstitute->area_title;
		}
	}
}
