<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Icrm;

use Schedule\Utilities\SingletonWrapperTrait;
use Windwalker\DI\Container;
use Windwalker\System\ExtensionHelper;

/**
 * Class SdkHelper
 *
 * @since 1.0
 */
abstract class SdkHelper
{
	/**
	 * Using SingletonWrapperTrait.
	 */
	use SingletonWrapperTrait;

	/**
	 * getSdk
	 *
	 * @param Container $container
	 *
	 * @return \Schedule\Icrm\Sdk
	 */
	public static function getSdk(Container $container = null)
	{
		$container = $container ? : Container::getInstance('com_schedule');

		$params = ExtensionHelper::getParams('com_schedule');

		$host = $params->get('icrm_api.host');

		if (strpos($host, 'http') === false)
		{
			$host = 'http://' . $host;
		}

		return static::getInstance('\\Schedule\\Icrm\\Sdk', $host, $container);
	}
}
