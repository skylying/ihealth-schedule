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
	 * @return  mixed
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
	 * @return  mixed
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
}
