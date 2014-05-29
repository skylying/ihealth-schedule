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
	 * Property filteerFields.
	 *
	 * @var  array
	 */
	protected $filterFields = array(
		'customer_id',
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.prescriptions.helper.query', Container::FORCE_NEW);

		$this->addTable('prescription', Table::PRESCRIPTIONS);

		$this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());
	}

	/**
	 * Override query string, because we don't need all the fileds in prescription
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
		$prescriptionsApiFields = array(
			'`id`',
			'`hospital_id`',
			'`id_number`',
			'`birth_date`',
			'`see_dr_date`',
			'`period`',
			'`times`',
			'`deliver_nths`',
			'`method`',
			'`empty_date_1st`',
			'`empty_date_2nd`',
			'`note`',
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

		foreach ($this->items as $singleRx)
		{
			$singleRx->schedules = $this->getSchedules($singleRx->id);

			$singleRx->drugs = $this->getDrugs($singleRx->id);
		}

		return $this->items;
	}

	/**
	 * Get schedules using $rxid
	 *
	 * @param int $rxId
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
	 * Get drugs using $rxId
	 *
	 * @param int $rxId
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
	 * @param string $ordering
	 * @param string $direction
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);

		$container = $this->getContainer();
		$input = $container->get('input');

		// "id" here actually means "customer_id"
		$customerId = (int) $input->get('id');

		$filters = $this->state->get('filter', array());

		if (! empty($customerId))
		{
			$filters['customer_id'] = $customerId;
		}

		$this->state->set('filter', $filters);
	}

	/**
	 * configureFilters
	 *
	 * @param \Windwalker\Model\Filter\FilterHelper $filterHelper
	 *
	 * @return  void
	 */
	protected function configureFilters($filterHelper)
	{
		$filterHelper->setHandler(
			'customer_id',
			function ($query, $field, $value)
			{
				/** @var $query \JDatabaseQuery */
				$query->where('`prescription`.`customer_id`=' . (int) $value);
			}
		);

		parent::configureFilters($filterHelper);
	}
}
