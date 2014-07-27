<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Table\Table;
use Windwalker\DI\Container;
use Windwalker\Model\Helper\QueryHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelPrescriptions
 *
 * @since 1.0
 */
class ScheduleModelPrescriptions extends \Windwalker\Model\ListModel
{
	/**
	 * Property filterMapping.
	 *
	 * @var  array
	 */
	protected $filterMapping = array(
		'customer' => 'prescription.customer_id',
		'member_id' => 'map.member_id',
		'hospital_id' => 'prescription.hospital_id',
		'id_number' => 'prescription.id_number',
		'method' => 'prescription.method',
		'times' => 'prescription.times',
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.prescriptions.helper.query', Container::FORCE_NEW);

		$this->addTable('prescription', Table::PRESCRIPTIONS)
			->addTable('map', Table::CUSTOMER_MEMBER_MAPS, 'prescription.customer_id = map.customer_id');

		$this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());
	}

	/**
	 * Override query string, because we don't need all the fileds in prescription
	 *
	 * @param   JDatabaseQuery $query
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
		$prescriptionsApiFields = array(
			'prescription.`id`',
			'prescription.`hospital_id`',
			'prescription.`id_number`',
			'prescription.`birth_date`',
			'prescription.`see_dr_date`',
			'prescription.`period`',
			'prescription.`times`',
			'prescription.`deliver_nths`',
			'prescription.`method`',
			'prescription.`empty_date_1st`',
			'prescription.`empty_date_2nd`',
			'prescription.`note`',
			'map.`member_id` AS `member_id`'
		);

		// Reset select and replace actual fields we need
		$query->clear('select')
			->select($prescriptionsApiFields);
	}

	/**
	 * Get prescriptions (include prescriptions, schedules, drugs)
	 *
	 * @return  mixed
	 */
	public function getItems()
	{
		$this->items = parent::getItems();

		foreach ($this->items as $rx)
		{
			$rx->schedules = $this->getSchedules($rx->id);

			$rx->drugs = $this->getDrugs($rx->id);
		}

		return $this->items;
	}

	/**
	 * Get schedules by $rxId
	 *
	 * @param   int $rxId
	 *
	 * @return  array
	 */
	protected function getSchedules($rxId)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$scheduleApiFields = array(
			'`id`',
			'`city`',
			'`city_title`',
			'`area`',
			'`area_title`',
			'`address`',
			'`date`',
			'`deliver_nth`',
			'`drug_empty_date`',
			'`session`',
			'`status`',
			'`cancel`',
			'`cancel_note`',
		);

		$query->select($scheduleApiFields)
			->from(Table::SCHEDULES . ' AS schedule')
			->where('`schedule`.`rx_id` = ' . $rxId);

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * Get drugs by $rxId
	 *
	 * @param   int $rxId
	 *
	 * @return  array
	 */
	protected function getDrugs($rxId)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$drugApiFields = array('`hicode`', '`quantity`');

		$query->select($drugApiFields)
			->from(Table::DRUGS . ' AS drug')
			->where('`drug`.`rx_id` = ' . $rxId);

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * populateState
	 *
	 * @param   string $ordering
	 * @param   string $direction
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$input = $this->getContainer()->get('input');

		// Set filters
		foreach ($this->filterMapping as $request => $field)
		{
			$_REQUEST['filter'][$field] = $input->get($request);
		}

		parent::populateState($ordering, $direction);
	}
}
