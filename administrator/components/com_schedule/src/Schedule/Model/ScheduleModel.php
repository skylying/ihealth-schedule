<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Schedule\Model;

use Windwalker\Model\AdminModel;
use Schedule\Table\Collection AS TableCollection;

// No direct access
defined('_JEXEC') or die;

/**
 * Class Schedule\Model\ScheduleModel
 *
 * @since 1.0
 */
class ScheduleModel extends AdminModel
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
	protected $name = 'schedule';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'schedule';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'schedules';

	/**
	 * Method to set new item ordering as first or last.
	 *
	 * @param   \JTable $table    Item table to save.
	 * @param   string  $position 'first' or other are last.
	 *
	 * @return  void
	 */
	public function setOrderPosition($table, $position = 'last')
	{
		parent::setOrderPosition($table, $position);
	}

	/**
	 * getForm
	 *
	 * @param   array $data
	 * @param   bool  $loadData
	 *
	 * @return  mixed
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$config = array(
			'control'   => 'jform',
			'load_data' => $loadData
		);

		$formName = $this->state->get('form.type', 'schedule_institute');

		return $this->loadForm($this->option . '.' . $formName . '.form', $formName, $config);
	}

	/**
	 * getFormInstitute
	 *
	 * @param   array  $data
	 * @param   bool   $loadData
	 *
	 * @return  \JForm
	 *
	 * @TODO rename to "getInstituteForm"
	 */
	public function getFormInstitute($data = array(), $loadData = true)
	{
		$config = array(
			'control'   => 'jform',
			'load_data' => $loadData
		);

		$formName = 'schedule_institute';

		return $this->loadForm($this->option . '.' . $formName . '.form', $formName, $config);
	}

	/**
	 * getFormIndividual
	 *
	 * @param   array  $data
	 * @param   bool   $loadData
	 *
	 * @return  \JForm
	 *
	 * @TODO rename to "getIndividualForm"
	 */
	public function getFormIndividual($data = array(), $loadData = true)
	{
		$config = array(
			'control'   => 'jform',
			'load_data' => $loadData
		);

		$formName = 'schedule_individual';

		return $this->loadForm($this->option . '.' . $formName . '.form', $formName, $config);
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   \JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	public function prepareTable(\JTable $table)
	{
		$formName = $this->state->get('form.type', 'schedule_institute');

		if ('schedule_institute' === $formName)
		{
			$this->prepareInstituteTable($table);
		}
		else
		{
			$this->prepareIndividualTable($table);
		}

		$senderId = $this->state->get('sender_id', 0);

		if ($senderId > 0)
		{
			$tableSender = $this->getTable('Sender');
			$tableSender->load($senderId);

			$table->sender_name = $tableSender->name;
		}
	}

	/**
	 * Prepare and sanitise the table data prior to save institute data.
	 *
	 * @param   \JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	public function prepareInstituteTable(\JTable $table)
	{
		$instituteTable = TableCollection::loadTable('Institute', $table->institute_id);

		$table->institute_title = $instituteTable->short_title;
		$table->route_id        = $instituteTable->route_id;
		$table->city            = $instituteTable->city;
		$table->city_title      = $instituteTable->city_title;
		$table->area            = $instituteTable->area;
		$table->area_title      = $instituteTable->area_title;
		$table->address         = $instituteTable->address;
		$table->sender_name     = $instituteTable->sender_name;
	}

	/**
	 * Prepare and sanitise the table data prior to save individual data.
	 *
	 * @param   \JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	public function prepareIndividualTable(\JTable $table)
	{
		$customerTable = TableCollection::loadTable('Customer', $table->customer_id);
		$memberTable   = TableCollection::loadTable('Member', $table->member_id);
		$addressTable  = TableCollection::loadTable('Address', $table->address_id);

		$table->customer_name = $customerTable->name;
		$table->member_name   = $memberTable->name;
		$table->address       = $addressTable->address;
		$table->city          = $addressTable->city;
		$table->area          = $addressTable->area;
		$table->city_title    = $addressTable->city_title;
		$table->area_title    = $addressTable->area_title;
	}
}
