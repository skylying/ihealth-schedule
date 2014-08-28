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
	 * prepareTable
	 *
	 * @param JTable $table
	 *
	 * @return  void
	 */
	public function prepareTable(JTable $table)
	{
		$customer = (new DataMapper(Table::CUSTOMERS))->findOne($table->customer_id);

		// 客戶名
		$table->customer_name = $customer->name;

		// Id_number to upper case
		$table->id_number = strtoupper($table->id_number);

		$member = (new DataMapper(Table::MEMBERS))->findOne($table->member_id);

		// 會員名
		$table->member_name = $member->name;

		$hospital = (new DataMapper(Table::HOSPITALS))->findOne($table->hospital_id);

		// 醫院名
		$table->hospital_title = $hospital->title;

		// 客戶類型
		$table->type = "individual";

		parent::prepareTable($table);
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
		if (empty($returnVal) || empty($returnVal->id))
		{
			return $returnVal;
		}

		if (! empty($returnVal->id))
		{
			// 健保 code
			$drugs = (new DataMapper(Table::DRUGS))->find(array("rx_id" => $returnVal->id));

			// Set json
			$returnVal->drug = json_encode(iterator_to_array($drugs));
		}

		if (! empty($returnVal->id))
		{
			// RX images
			$images = (new DataMapper(Table::IMAGES))->find(array("rx_id" => $returnVal->id));

			// Set json
			$returnVal->ajax_image1 = isset($images[0]) ? $images[0]->id : null;
			$returnVal->ajax_image2 = isset($images[1]) ? $images[1]->id : null;
			$returnVal->ajax_image3 = isset($images[2]) ? $images[2]->id : null;
		}

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
				"tel_office"      => $schedule->tel_office,
				"tel_home"        => $schedule->tel_home,
				"mobile"          => $schedule->mobile,
				"deliver_nth"     => array($schedule->deliver_nth)
			);
		}

		return $returnVal;
	}

	/**
	 * getSchedulesForm
	 *
	 * @param   array  $data
	 * @param   bool   $loadData
	 *
	 * @return  \JForm
	 */
	public function getSchedulesForm($data = array(), $loadData = true)
	{
		$config = array(
			'control'   => 'jform',
			'load_data' => $loadData
		);

		$formName = 'rxindividual_schedules';

		return $this->loadForm($this->option . '.' . $formName . '.form', $formName, $config);
	}
}
