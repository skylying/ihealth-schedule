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
}
