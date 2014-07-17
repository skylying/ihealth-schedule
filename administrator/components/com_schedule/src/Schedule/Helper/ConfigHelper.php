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
	 */
	public static function getDefaultSender()
	{
		$config = \JComponentHelper::getParams('com_schedule')->get("icrm");

		$defaultSenderFromConfig = (strpos($config->default_sender, '-')) ? $config->default_sender : '0-0';

		$defaultSender = explode('-', $defaultSenderFromConfig);

		$defaultSenderId = $defaultSender[0];
		$defaultSenderName = $defaultSender[1];

		return array(
			'id' => $defaultSenderId,
			'sender' => $defaultSenderName
		);
	}

	/**
	 * getNotifyEmptyRouteMails
	 *
	 * @return  array
	 */
	public static function getNotifyEmptyRouteMails()
	{
		static $mails = null;

		if (is_array($mails))
		{
			return $mails;
		}

		$config = \JComponentHelper::getParams('com_schedule')->get("schedule");

		$mails = (isset($config->empty_route_mail) ? $config->empty_route_mail : '');
		$mails = explode(' ', $mails);

		return $mails;
	}
}
