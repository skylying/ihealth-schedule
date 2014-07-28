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
		$defaultSender = \JComponentHelper::getParams('com_schedule')->get("icrm.default_sender");

		$defaultSenderFromConfig = (strpos($defaultSender, '-') !== false) ? $defaultSender : '0-0';

		list($id, $name) = explode('-', $defaultSenderFromConfig);

		return array(
			'id' => $id,
			'sender' => $name
		);
	}
}
