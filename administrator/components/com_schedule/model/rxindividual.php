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
 * Class ScheduleModelRxindividual
 *
 * @since 1.0
 */
class ScheduleModelRxindividual extends AdminModel
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
	protected $name = 'rxindividual';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'rxindividual';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'rxindividuals';

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
	 * loadFormData
	 *
	 * @return  array
	 */
	protected function loadFormData()
	{
		$returnVal = parent::loadFormData();

		// 如果取得非 object 轉為 stdClass object
		if (! is_object($returnVal))
		{
			$returnVal = (object) $returnVal;
		}

		// 取得排程 table
		$schedule = $this->getTable("Schedule");

		foreach (array("1st", "2nd", "3rd") as $val)
		{
			// 讀取對應排程
			$schedule->load(array("rx_id" => $returnVal->id, "deliver_nth" => $val));

			// 如果沒有對應排程執行下一筆
			if (empty($schedule->id))
			{
				continue;
			}

			// Std Class method
			$method = "schedules_{$val}";

			// 塞入資料
			$returnVal->$method = (object) array(
				"address"      => $schedule->address_id,
				"empty_date"   => $schedule->drug_empty_date,
				"send_date"    => $schedule->date,
				"send_time"    => $schedule->session,
				"deliver_nths" => array($schedule->deliver_nth)
			);
		}

		return $returnVal;
	}
}
