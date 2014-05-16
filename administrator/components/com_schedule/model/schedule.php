<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Model\AdminModel;
use Schedule\Table\Collection AS TableCollection;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelSchedule
 *
 * @since 1.0
 */
class ScheduleModelSchedule extends AdminModel
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
	 * @return  JForm
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
	 * @return  JForm
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
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	public function prepareTable(JTable $table)
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

		$senderId = JFactory::getApplication()->input->get('sender_id', 0);

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
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	public function prepareInstituteTable($table)
	{
		$tableInstitute = TableCollection::loadTable('Institute', $table->institute_id);

		$table->institute_title = $tableInstitute->title;
		$table->route_id        = $tableInstitute->route_id;
		$table->city            = $tableInstitute->city;
		$table->city_title      = $tableInstitute->city_title;
		$table->area            = $tableInstitute->area;
		$table->area_title      = $tableInstitute->area_title;
		$table->address         = $tableInstitute->address;
		$table->sender_name     = $tableInstitute->sender_name;
	}

	/**
	 * Prepare and sanitise the table data prior to save individual data.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	public function prepareIndividualTable($table)
	{
		$tableCustomer = $this->getTable('Customer');
		$tableCustomer->load($table->customer_id);

		$tableMember = $this->getTable('Member');
		$tableMember->load($table->member_id);

		$tableCity = $this->getTable('City');
		$tableCity->load($table->city);

		$tableArea = $this->getTable('Area');
		$tableArea->load($table->area);

		$table->customer_name = $tableCustomer->name;
		$table->member_name = $tableMember->name;
		$table->city_title = $tableCity->title;
		$table->area_title = $tableArea->title;
	}

	/**
	 * Overwrite save method
	 *
	 * @param array $data
	 *
	 * @return  bool
	 *
	 * @throws Exception
	 */
	public function save($data)
	{
		// Workaround: deliver_nth cannot be saved in array format, possibly because the table structure is enum?
		$data['deliver_nth'] = $data['deliver_nth'][0];

		$container  = $this->getContainer();
		$table      = $this->getTable();
		$dispatcher = $container->get('event.dispatcher');

		if ((!empty($data['tags']) && $data['tags'][0] != ''))
		{
			$table->newTags = $data['tags'];
		}

		$key = $table->getKeyName();
		$pk  = \JArrayHelper::getValue($data, $key, $this->getState($this->getName() . '.id'));

		$isNew = true;

		// Include the content plugins for the on save events.
		\JPluginHelper::importPlugin('content');

		// Load the row if saving an existing record.
		if ($pk)
		{
			$table->load($pk);
			$isNew = false;
		}

		// Bind the data.
		$table->bind($data);

		// Prepare the row for saving
		$this->prepareTable($table);

		// Check the data.
		if (!$table->check())
		{
			throw new \Exception($table->getError());
		}

		// Trigger the onContentBeforeSave event.
		$result = $dispatcher->trigger($this->eventBeforeSave, array($this->option . '.' . $this->name, $table, $isNew));

		if (in_array(false, $result, true))
		{
			throw new \Exception($table->getError());
		}

		// Store the data.
		if (!$table->store())
		{
			throw new \Exception($table->getError());
		}

		// Clean the cache.
		$this->cleanCache();

		// Trigger the onContentAfterSave event.
		$dispatcher->trigger($this->eventAfterSave, array($this->option . '.' . $this->name, $table, $isNew));

		$pkName = $table->getKeyName();

		if (isset($table->$pkName))
		{
			$this->state->set($this->getName() . '.id', $table->$pkName);
		}

		$this->state->set($this->getName() . '.new', $isNew);

		$this->postSaveHook($table);

		return true;
	}
}
