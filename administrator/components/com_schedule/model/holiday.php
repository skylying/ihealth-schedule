<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Helper\DateHelper;
use Windwalker\Model\AdminModel;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelHoliday
 *
 * @since 1.0
 */
class ScheduleModelHoliday extends AdminModel
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
	protected $name = 'holiday';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'holiday';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'holidays';

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
		$date = DateHelper::getDate($table->date);

		$table->year  = $date->format('Y', true);
		$table->month = $date->format('m', true);
		$table->day   = $date->format('d', true);

		$weekDay = strtoupper($date->format('D', true, false));

		if ($weekDay == 'SAT' || $weekDay == 'SUN')
		{
			$table->title = '週末';
		}

		$table->weekday = $weekDay;
	}
}
