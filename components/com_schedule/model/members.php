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
 * Class ScheduleModelMember
 *
 * @since 1.0
 */
class ScheduleModelMembers extends \Windwalker\Model\ListModel
{
	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$this->addTable('member', Table::MEMBERS);
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