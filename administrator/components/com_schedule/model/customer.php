<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Model\AdminModel;
use Windwalker\Joomla\DataMapper\DataMapper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelCustomer
 *
 * @since 1.0
 */
class ScheduleModelCustomer extends AdminModel
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
	protected $name = 'customer';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'customer';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'customers';

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
	 * getItem
	 *
	 * @param   int  $pk
	 *
	 * @return  mixed
	 */
	public function getItem($pk = null)
	{
		$this->item = parent::getItem($pk);

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		if (empty($this->item->id))
		{
			return $this->item;
		}

		// Get member id list
		$query->select('`map`.`member_id`')
			->from("#__schedule_customers AS customer")
			->join('LEFT', $db->quoteName('#__schedule_customer_member_maps') . ' AS map ON customer.id = map.customer_id')
			->where('`map`.`customer_id`= ' . $db->q($this->item->id));

		$this->item->members = $db->setQuery($query)->loadColumn();

		return $this->item;
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
		// 身分證字號大寫
		$table->id_number = isset($table->id_number) ? ucfirst($table->id_number) : "";

		$jformData = JFactory::getApplication()->input->get('jform', '', 'array');

		$customerType = \Windwalker\Helper\ArrayHelper::getValue($jformData, 'type');

		if ('individual' === $customerType)
		{
			$this->prepareCityTable($table);
		}
		else
		{
			$this->prepareInstituteTable($table);
		}

		parent::prepareTable($table);
	}

	/**
	 * Prepare and sanitise the table data prior to save institute data.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	public function prepareCityTable($table)
	{
		$tableCity = $this->getTable('City');
		$tableCity->load($table->city);

		$tableArea = $this->getTable('Area');
		$tableArea->load($table->area);

		$table->city_title = $tableCity->title;
		$table->area_title = $tableArea->title;
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
		$tableInstitute = $this->getTable('Institute');
		$tableInstitute->load($table->institute_id);

		$table->city_title = $tableInstitute->city_title;
		$table->area_title = $tableInstitute->area_title;

	}

	/**
	 * Prepare address json data from form
	 *
	 * @return  array
	 */
	protected function loadFormData()
	{
		$returnVal = parent::loadFormData();

		$addressMapper = new DataMapper(\Schedule\Table\Table::ADDRESSES);

		// Prepare empty string as json format
		$returnVal->address = '[]';

		if (!empty($returnVal->id))
		{
			$addressDataSet = $addressMapper->find(array("customer_id" => $returnVal->id));

			$addressReturnData = array();

			foreach ($addressDataSet as $addressData)
			{
				$addressReturnData[] = $addressData;
			}

			$returnVal->address = json_encode($addressReturnData);
		}

		return $returnVal;
	}
}
