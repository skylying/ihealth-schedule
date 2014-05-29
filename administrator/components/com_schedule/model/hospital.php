<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Model\AdminModel;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelHospital
 *
 * @since 1.0
 */
class ScheduleModelHospital extends AdminModel
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
	protected $name = 'hospital';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'hospital';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'hospitals';

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
	 * prepareTable
	 *
	 * @param JTable $table
	 *
	 * @return  void
	 */
	public function prepareTable(JTable $table)
	{
		$tableCity = $this->getTable('City');
		$tableCity->load($table->city);
		$table->city_title = $tableCity->title;

		$tableArea = $this->getTable('Area');
		$tableArea->load($table->area);
		$table->area_title = $tableArea->title;
	}
}
