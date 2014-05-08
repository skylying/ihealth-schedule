<?php
/**
 * Part of ihealth-schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Table;

/**
 * Class Collection
 */
class Collection
{
	/**
	 * Table class prefix.
	 *
	 * @var  string
	 */
	protected static $prefix = 'ScheduleTable';

	/**
	 * Store loaded tables.
	 *
	 * @var  \JTable[]
	 */
	protected static $tables = array();

	/**
	 * loadTable
	 *
	 * @param   string  $name  The table name.
	 * @param   mixed   $keys  An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                         set the instance property value is used.
	 *
	 * @return  \JTable
	 */
	public static function loadTable($name, $keys = null)
	{
		$tableHash = $name . serialize($keys);

		if (isset(static::$tables[$tableHash]))
		{
			return static::$tables[$tableHash];
		}

		static::$tables[$tableHash] = \JTable::getInstance($name, static::$prefix);
		static::$tables[$tableHash]->load($keys);

		return static::$tables[$tableHash];
	}
}
