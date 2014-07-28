<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

/**
 * Class ConfigHelper
 *
 * @since 1.0
 */
class ConfigHelper
{
	/**
	 * getDefaultSender
	 *
	 * @return  array
	 *
	 * @deprecated Use SendHelper::getDefaultSender() instead.
	 */
	public static function getDefaultSender()
	{
		return SenderHelper::getDefaultSender();
	}
}
