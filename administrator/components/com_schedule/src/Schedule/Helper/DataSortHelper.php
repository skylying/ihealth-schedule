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
	 * 取得欄位變更前得主索引值
	 *
	 * 範例
	 * $dataset = [{id: 1, val: 1}, {id: 2, val: 1}, {id: 3, val: 2}, {id: 4, val: 2}, {id: 5, val: 1}]
	 * $column  = "val"
	 * $index   = "id"
	 *
	 * 會取得
	 * [3, 4]
	 *
	 * @param   array   $dataset
	 * @param   string  $column
	 * @param   string  $index
	 *
	 * @return  array
	 */
	public static function getBeforeColumnChangeIndex($dataset, $column, $index = "id")
	{
		$indexCache = null;
		$valueCache = null;
		$returnVal  = array();

		foreach ($dataset as $data)
		{
			// If value change save last index
			if ($data->$column != $valueCache)
			{
				// If not setup
				if (null !== $indexCache)
				{
					$returnVal[] = $indexCache;
				}

				// Flush new id
				$valueCache = $data->$column;
			}

			// Flush index key
			$indexCache = $data->$index;
		}

		return $returnVal;
	}
}
