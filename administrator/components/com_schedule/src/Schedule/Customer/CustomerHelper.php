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
		// I rewrote this regex. Simple but accurate.
		if (!preg_match("/^[A-Z][0-9]{9}$/", $id))
		{
			return false;
		}

		if ($onlyFormat)
		{
			return true;
		}

		$head = array(
			'A' => '10',
			'B' => '11',
			'C' => '12',
			'D' => '13',
			'E' => '14',
			'F' => '15',
			'G' => '16',
			'H' => '17',
			'I' => '34',
			'J' => '18',
			'K' => '19',
			'M' => '21',
			'N' => '22',
			'O' => '35',
			'P' => '23',
			'Q' => '24',
			'T' => '27',
			'U' => '28',
			'V' => '29',
			'W' => '32',
			'X' => '30',
			'Z' => '33',
			'L' => '20',
			'R' => '25',
			'S' => '26',
			'Y' => '31',
		);

		$multiples = array(1, 9, 8, 7, 6, 5, 4, 3, 2, 1, 1);

		$id = $head[$id[0]] . substr($id, 1);
		$length = strlen($id);

		if ($length !== 11)
		{
			return false;
		}

		$sum = 0;

		for ($i = 0; $i < $length; ++$i)
		{
			$sum += $id[$i] * $multiples[$i];
		}

		return 0 === $sum % 10;
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
