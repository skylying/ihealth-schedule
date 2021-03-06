<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\DI\Container;
use Windwalker\Model\Filter\FilterHelper;
use Windwalker\Model\ListModel;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelRxindividuals
 *
 * @since 1.0
 */
class ScheduleModelRxindividuals extends ListModel
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
	protected $name = 'rxindividuals';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'rxindividual';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'rxindividuals';

	/**
	 * Property filterFields.
	 *
	 * @var array
	 */
	protected $filterFields = array(
		'see_dr_date_start',
		'see_dr_date_end'
	);

	/**
	 * Get List Query
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$q = parent::getListQuery();

		$q->group("rxindividual.id");

		return $q->where("rxindividual.`type` = 'individual'");
	}

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.rxindividuals.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('rxindividual', '#__schedule_prescriptions')
			->addTable('memberMap', '#__schedule_customer_member_maps', 'memberMap.customer_id    = rxindividual.customer_id')
			->addTable('member',    '#__schedule_members',              'member.id                = memberMap.member_id')
			->addTable('author',    '#__users',                         'rxindividual.created_by  = author.id')
			->addTable('modifier',  '#__users',                         'rxindividual.modified_by = modifier.id');

		$this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());
	}

	/**
	 * Post Get Query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
		$sql = <<<SQLALIAS
group_concat(
	CONCAT(
		'{',
			'"id": "',
				`member`.`id`,
			'",',

			'"name": "',
				`member`.`name`,
			'"',
		'}'
	)
) AS `member_json`
SQLALIAS;

		$query->select($sql);

		parent::postGetQuery($query);
	}

	/**
	 * getItems
	 *
	 * @return  mixed
	 */
	public function getItems()
	{
		$items = parent::getItems();

		$this->addExpiredNths($items);

		return $items;
	}

	/**
	 * Method to get expired schedules
	 *
	 * @param array $items
	 *
	 * @return  void
	 */
	public function addExpiredNths($items)
	{
		$rxIdList = JArrayHelper::getColumn($items, 'id');
		$rxIds = (string) new JDatabaseQueryElement('IN()', $rxIdList);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('`schedule`.`rx_id`, GROUP_CONCAT(`schedule`.`deliver_nth`) AS `expired_nths`')
			->from('#__schedule_schedules AS schedule')
			->where('`schedule`.`date` < NOW()')
			->group('`schedule`.`rx_id`');

		if ($rxIdList)
		{
			$query->where('`schedule`.`rx_id`' . $rxIds);
		}

		$expiredNths = array();

		foreach ($db->setQuery($query)->loadObjectList() as $data)
		{
			$expiredNths[$data->rx_id] = $data->expired_nths;
		}

		foreach ($items as &$item)
		{
			$item->expired_nths = JArrayHelper::getValue($expiredNths, $item->id, '');
		}
	}

	/**
	 * populateState
	 *
	 * @param string $ordering
	 * @param string $direction
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'rxindividual.created', $direction = 'DESC')
	{
		// Build ordering prefix
		if (!$ordering)
		{
			$table = $this->getTable('Rxindividual');

			$ordering = property_exists($table, 'ordering') ? 'rxindividual.created' : 'rxindividual.id';

			$ordering = property_exists($table, 'catid') ? 'rxindividual.catid, ' . $ordering : $ordering;
		}

		parent::populateState($ordering, $direction);
	}

	/**
	 * processFilters
	 *
	 * @param JDatabaseQuery $query
	 * @param array          $filters
	 *
	 * @return  JDatabaseQuery
	 */
	protected function processFilters(\JDatabaseQuery $query, $filters = array())
	{
		// If no state filter, set published >= 0
		if (!isset($filters['rxindividual.state']) && property_exists($this->getTable(), 'state'))
		{
			$query->where($query->quoteName('rxindividual.state') . ' >= 0');
		}

		return parent::processFilters($query, $filters);
	}

	/**
	 * configureFilters
	 *
	 * @param FilterHelper $filterHelper
	 *
	 * @return  void
	 */
	protected function configureFilters($filterHelper)
	{
		$filterHelper->setHandler(
			'see_dr_date_start',
			function ($query, $field, $min)
			{
				if ($min)
				{
					$query->where('`rxindividual`.`see_dr_date` >= ' . $query->q($min));
				}
			}
		);

		$filterHelper->setHandler(
			'see_dr_date_end',
			function($query, $field, $max)
			{
				if ($max)
				{
					$query->where('`rxindividual`.`see_dr_date` <= ' . $query->q($max));
				}
			}
		);
	}

	/**
	 * configureSearches
	 *
	 * @param \Windwalker\Model\Filter\SearchHelper $searchHelper
	 *
	 * @return  void
	 */
	protected function configureSearches($searchHelper)
	{
	}
}
