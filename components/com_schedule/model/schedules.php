<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Table\Table;
use Windwalker\Model\Helper\QueryHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelSchedules
 *
 * @since 1.0
 */
class ScheduleModelSchedules extends \Windwalker\Model\ListModel
{
	/**
	 * Property filterFields.
	 *
	 * @var  array
	 */
	protected $filterFields = array(
		'rx_id',
		'member_id'
	);

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$this->addTable('schedule', Table::SCHEDULES);
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
		$input = $this->getContainer()->get('input');

		$rx_id = $input->get('rx_id');
		$member_id = $input->get('member_id');

		// Set filter:
		if (!empty($rx_id))
		{
			$_REQUEST['filter']['rx_id'] = $rx_id;
		}

		if (!empty($member_id))
		{
			$_REQUEST['filter']['member_id'] = $member_id;
		}

		parent::populateState($ordering, $direction);
	}

	/**
	 * postGetQuery
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
		$queryHelper = $this->container->get('model.' . $this->getName() . '.helper.query');

		// Reset select to avoid redundant columns
		$query->clear('select')
			->select($queryHelper->getSelectFields(QueryHelper::COLS_WITH_FIRST));
	}
}
