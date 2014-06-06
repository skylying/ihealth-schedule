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
	 * 用陣列中物件的 id 當 array 的 key 值
	 *
	 * @param   Data[] &$dataset
	 *
	 * @return  void
	 */
	public static function orderArrayByObjectId(&$dataset)
	{
		$datasetCopy = $dataset;

		$dataset = array();

		foreach ($datasetCopy as $data)
		{
			$dataset[$data->id] = $data;
		}
	}

	/**
	 * 取得陣列中 $column 值為 $value 的物件
	 *
	 * eg:
	 *
	 * ```
	 * $dataset = [{task: 1}, {task: 1}, {task: 2}]
	 * $column = task
	 * $value = 1
	 * ```
	 *
	 * 會取得
	 *
	 * ```
	 * [{task: 1}, {task: 1}]
	 * ```
	 *
	 * @param   Data[]  $dataset
	 * @param   string  $column
	 * @param   mixed   $value
	 *
	 * @return  array
	 */
	public static function getArrayContentByObjectColumn($dataset, $column, $value)
	{
		$returnArray = array();

		foreach ($dataset as $data)
		{
			if ($value === $data->$column)
			{
				$returnArray[] = $data;
			}
		}

		return $returnArray;
	}
}
