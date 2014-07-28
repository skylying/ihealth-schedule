<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Customer;

/**
 * Class CustomerHelper
 *
 * @since 1.0
 */
class CustomerHelper
{
	/**
	 * verifyIdNumber
	 *
	 * @param string $id
	 * @param bool   $onlyFormat
	 *
	 * @note code from: http://mesak.tw/code/php/628/id-number-verification-lite
	 *
	 * @return  bool
	 */
	public static function verifyIdNumber($id, $onlyFormat = true)
	{
		$iPidLen = strlen($id);

		// I rewrote this regex. Simple but accurate.
		if (!preg_match("/^[A-Z]+[0-9]{9}$/", $id))
		{
			return false;
		}

		if ($onlyFormat)
		{
			return true;
		}

		$head = array(
			"A" => 1,
			"B" => 10,
			"C" => 19,
			"D" => 28,
			"E" => 37,
			"F" => 46,
			"G" => 55,
			"H" => 64,
			"I" => 39,
			"J" => 73,
			"K" => 82,
			"M" => 11,
			"N" => 20,
			"O" => 48,
			"P" => 29,
			"Q" => 38,
			"T" => 65,
			"U" => 74,
			"V" => 83,
			"W" => 21,
			"X" => 3,
			"Z" => 30,
			"L" => 2,
			"R" => 47,
			"S" => 56,
			"Y" => 12
		);

		$pid  = strtoupper($id);

		$iSum = 0;

		for ($i = 0; $i < $iPidLen; $i++)
		{
			$sIndex = substr($pid, $i, 1);

			$iSum += (empty($i)) ? $head[$sIndex] : intval($sIndex) * abs(9 - base_convert($i, 10, 9));
		}

		return ($iSum % 10 == 0) ? true : false;
	}

	/**
	 * createFakeId
	 *
	 * @note Code from: http://liaosankai.pixnet.net/blog/post/14514213-%E8%BA%AB%E4%BB%BD%E8%AD%89%E9%A9%97%E8%AD%89%E7%A8%8B%E5%BC%8F-for-php
	 *
	 * @return  string
	 */
	public static function createFakeId()
	{
		// 建立字母分數陣列
		$headPoint = array('A' => 1, 'I' => 39, 'O' => 48, 'B' => 10, 'C' => 19, 'D' => 28,
			'E' => 37, 'F' => 46, 'G' => 55, 'H' => 64, 'J' => 73, 'K' => 82,
			'L' => 2, 'M' => 11, 'N' => 20, 'P' => 29, 'Q' => 38, 'R' => 47,
			'S' => 56, 'T' => 65, 'U' => 74, 'V' => 83, 'W' => 21, 'X' => 3,
			'Y' => 12, 'Z' => 30
		);

		// 建立加權基數陣列
		$multiply = array(8, 7, 6, 5, 4, 3, 2, 1);

		// 取得隨機數字
		$number = mt_rand(1, 2);

		for ($i = 0; $i < 7; $i++)
		{
			$number .= mt_rand(0, 9);
		}

		// 切開字串
		$len = strlen($number);

		$stringArray = array();

		for ($i = 0; $i < $len; $i++)
		{
			$stringArray[$i] = substr($number, $i, 1);
		}

		// 取得隨機字母分數

		$index = chr(mt_rand(65, 90));
		$total = $headPoint[$index];

		// 取得數字分數
		$len = count($stringArray);

		for ($j = 0; $j < $len; $j++)
		{
			$total += $stringArray[$j] * $multiply[$j];
		}

		// 取得檢查比對碼
		if ($total % 10 == 0)
		{
			return $index . $number . 0;
		}
		else
		{
			return $index . $number . (10 - $total % 10);
		}
	}
}
