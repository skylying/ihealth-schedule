<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Model\AdminModel;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Table\Table;

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

		// 如果沒值就直接回傳
		if (empty($returnVal))
		{
			return $returnVal;
		}

		// 健保 code
		$drugs = (new DataMapper(Table::DRUGS))->find(array("rx_id" => $returnVal->id));

		$drugDataSet = array();

		// 把 object->data 整理成 array
		foreach ($drugs as $drug)
		{
			$drugDataSet[] = $drug;
		}

		// Set json
		$returnVal->drug = json_encode($drugDataSet);

		foreach (array("1st", "2nd", "3rd") as $val)
		{
			// 取得排程 table
			$schedule = $this->getTable("Schedule");

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
				"schedule_id"     => $schedule->id,
				"address_id"      => $schedule->address_id,
				"drug_empty_date" => $schedule->drug_empty_date,
				"date"            => $schedule->date,
				"session"         => $schedule->session,
				"deliver_nth"     => array($schedule->deliver_nth)
			);
		}

		return $returnVal;
	}
}
