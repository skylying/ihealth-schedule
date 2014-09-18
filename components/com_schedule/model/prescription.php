<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Windwalker\Helper\DateHelper;
use Schedule\Table\Table;
use Schedule\Table\Collection as TableCollection;

/**
 * Class ScheduleModelPrescription
 *
 * @since 1.0
 */
class ScheduleModelPrescription extends \Windwalker\Model\AdminModel
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
	protected $name = 'prescription';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'prescription';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'prescriptions';

	/**
	 * getSchedules
	 *
	 * @param   int  $rxId  Prescription id
	 *
	 * @return  array
	 */
	public function getSchedules($rxId)
	{
		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('schedule.*')
			->from(Table::SCHEDULES . ' AS schedule')
			->where('`schedule`.`rx_id`=' . (int) $rxId);

		$schedules = $db->setQuery($query)->loadObjectList();

		foreach ($schedules as $schedule)
		{
			$schedule->params = (array) json_decode($schedule->params);
		}

		return $schedules;
	}

	/**
	 * getDrugs
	 *
	 * @param   int  $rxId  Prescription id
	 *
	 * @return  array
	 */
	public function getDrugs($rxId)
	{
		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('drug.*')
			->from(Table::DRUGS . ' AS drug')
			->where('`drug`.`rx_id`=' . (int) $rxId);

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * getImages
	 *
	 * @param   int  $rxId  Prescription id
	 *
	 * @return  array
	 */
	public function getImages($rxId)
	{
		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('image.*')
			->from(Table::IMAGES . ' AS image')
			->where('`image`.`rx_id`=' . (int) $rxId);

		$images = $db->setQuery($query)->loadObjectList();

		foreach ($images as &$image)
		{
			if (!preg_match('#^(http|https|ftp)://#i', $image->path))
			{
				$image->path = JUri::root() . $image->path;
			}
		}

		return $images;
	}

	/**
	 * Get drug form object to perform validation
	 *
	 * @return  \JForm
	 */
	public function getDrugForm()
	{
		$config = array(
			'control'   => 'jform',
			'load_data' => false,
		);

		$formName = 'prescription_drug';

		return $this->loadForm($this->option . '.' . $formName . '.form', $formName, $config);
	}

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

		$customerTable = TableCollection::loadTable('Customer', $table->customer_id);
		$memberTable   = TableCollection::loadTable('Member',   $table->member_id);
		$hospitalTable = TableCollection::loadTable('Hospital', $table->hospital_id);

		$emptyDate1st = DateHelper::getDate($table->see_dr_date);
		$emptyDate2nd = DateHelper::getDate($table->see_dr_date);

		$emptyDate1st->modify('+' . $table->period . ' days');
		$emptyDate2nd->modify('+' . ($table->period * 2) . ' days');

		$table->customer_name = $customerTable->name;
		$table->member_name = $memberTable->name;
		$table->hospital_title = $hospitalTable->title;
		$table->id_number = $customerTable->id_number;
		$table->birth_date = $customerTable->birth_date;
		$table->deliver_nths = implode(',', (array) $table->deliver_nths);
		$table->empty_date_1st = $emptyDate1st->toSql(true);
		$table->empty_date_2nd = $emptyDate2nd->toSql(true);
	}
}
