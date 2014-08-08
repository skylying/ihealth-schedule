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
 * Class ScheduleModelRxresident
 *
 * @since 1.0
 */
class ScheduleModelRxresident extends AdminModel
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
	protected $name = 'rxresident';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'rxresident';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'rxresidents';

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
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since   3.2
	 */
	public function getForm($data = array(), $loadData = false)
	{
		$config = array(
			'control'   => 'jform',
			'load_data' => $loadData,
		);

		// For form hash. @see \Windwalker\Model\FormModel (line:76)
		$config['data.id'] = JArrayHelper::getValue($data, 'id', '');

		$key = $this->option . '.' . $this->getName() . '.form.' . $config['data.id'];

		$form = $this->loadForm($key, $this->getName(), $config);

		$form->bind($data);

		return $form;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	protected function prepareTable(JTable $table)
	{
		parent::prepareTable($table);

		$tableInstitute = TableCollection::loadTable('Institute', $table->institute_id);
		$tableCustomer  = TableCollection::loadTable('Customer', $table->customer_id);

		$table->type                  = 'resident';
		$table->delivered             = 0;
		$table->called                = 0;
		$table->received              = 1;
		$table->deliver_nths          = implode(',', (array) $table->deliver_nths);
		$table->institute_short_title = $tableInstitute->short_title;
		$table->customer_name         = $tableCustomer->name;

		if (empty($table->empty_date_1st))
		{
			$modify = sprintf('+%s day', $table->period);

			$table->empty_date_1st = (new JDate($table->see_dr_date))->modify($modify)->toSql();
		}

		if (empty($table->empty_date_2nd))
		{
			$modify = sprintf('+%s day', $table->period * 2);

			$table->empty_date_2nd = (new JDate($table->see_dr_date))->modify($modify)->toSql();
		}
	}
}
