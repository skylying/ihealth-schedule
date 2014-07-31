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
	 * @note Reference: http://zh.wikipedia.org/wiki/%E4%B8%AD%E8%8F%AF%E6%B0%91%E5%9C%8B%E5%9C%8B%E6%B0%91%E8%BA%AB%E5%88%86%E8%AD%89
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
	 * @note Reference: http://zh.wikipedia.org/wiki/%E4%B8%AD%E8%8F%AF%E6%B0%91%E5%9C%8B%E5%9C%8B%E6%B0%91%E8%BA%AB%E5%88%86%E8%AD%89
	 *
	 * @return  string
	 */
	public static function createFakeId()
	{
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

		// Show me the initial character
		$id = chr(mt_rand(65, 90));

		// Show me the gender code
		$id .= mt_rand(1, 2);

		// Show me 7 random numbers
		for ($i = 0; $i < 7; ++$i)
		{
			$id .= mt_rand(0, 9);
		}

		$sum = 0;
		$tmp = $head[$id[0]] . substr($id, 1);

		for ($i = 0; $i < 10; ++$i)
		{
			$sum += $tmp[$i] * $multiples[$i];
		}

		$check = $sum % 10;

		// Show me the last number
		return $id . ((0 === $check) ? 0 : 10 - $check);
	}
}
