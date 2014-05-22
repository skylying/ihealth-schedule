<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

/**
 * Quick constants for every table's name.
 *
 * @since 1.0
 */
abstract class ApiReturnCodeHelper
{
	/**
	 * SUCCESS
	 *
	 * @var  string
	 */
	const SUCCESS_ROUTE_EXIST = '0';

	/**
	 * ERROR_NO_ROUTE
	 *
	 * @var  string
	 */
	const ERROR_NO_ROUTE = '1';

	/**
	 * ERROR_NO_SEE_DR_DATE
	 *
	 * @var  string
	 */
	const ERROR_NO_SEE_DR_DATE = '2';
}
