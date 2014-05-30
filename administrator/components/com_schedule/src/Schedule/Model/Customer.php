<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Schedule\Model;

use Windwalker\Model\AdminModel;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Table\Table;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelCustomer
 *
 * @since 1.0
 */
class Customer extends AdminModel
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
	 * getItem
	 *
	 * @param   int  $pk
	 *
	 * @return  mixed
	 */
	public function getItem($pk = null)
	{
		$this->item = parent::getItem($pk);

		if (empty($this->item->id))
		{
			return $this->item;
		}

		// Get full address list
		$addressMapper = new DataMapper(Table::ADDRESSES);

		// Prepare empty string as json format
		$this->item->addresses = array();

		if (!empty($this->item->id))
		{
			$addressDataSet = $addressMapper->find(array("customer_id" => $this->item->id));

			$this->item->addresses = iterator_to_array($addressDataSet);
		}

		return $this->item;
	}

	/**
	 * prepareTable
	 *
	 * @param \JTable $table
	 *
	 * @return  void
	 */
	public function prepareTable(\JTable $table)
	{
		if ('individual' === $table->type)
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
	 * @param   \JTable  $table  A reference to a JTable object.
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
	 * @param   \JTable  $table  A reference to a JTable object.
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
}
