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
	protected function prepareTable($table)
	{
		/*
		 * TODO: 取得機構簡稱 (institute_short_title)
		 * TODO: 取得客戶姓名 (customer_name)
		 * TODO: 取得客戶身分證號碼 (id_number)
		 * TODO: 取得客戶開立處方醫院編號 (hospital_id)
		 * TODO: 取得客戶開立處方醫院名稱 (hospital_title)
		 * TODO: 計算第1次藥吃完日 (empty_date_1st)
		 * TODO: 計算第2次藥吃完日 (empty_date_2nd)
		 *
		 * TODO: 討論註記選項是否要儲存 (remind)
		 */

		$table->type = 'resident';
		$table->deliver_nths = implode(',', $table->deliver_nths);
	}
}
