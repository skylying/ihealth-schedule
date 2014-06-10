<?php
/**
 * Part of ihealth-schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

use Windwalker\Data\Data;

/**
 * Class DataSortHelper
 *
 * @since 1.0
 */
abstract class DataSortHelper
{
	/**
	 * getArrayAccessColumn
	 *
	 * @param   ArrayAccess  $object
	 * @param   string       $index
	 *
	 * @return  array
	 */
	public static function getArrayAccessColumn($object, $index)
	{
		$result = array();

		if (is_object($object))
		{
			foreach ($object as &$item)
			{
				if (is_array($item) && isset($item[$index]))
				{
					$result[] = $item[$index];
				}
				elseif (is_object($item) && isset($item->$index))
				{
					$result[] = $item->$index;
				}
				// Else ignore the entry
			}
		}

		return $result;
	}
}
