<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2014 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Schedule\Config;

use Joomla\Registry\Registry;
use Windwalker\Helper\PathHelper;

/**
 * The ConfigHelper class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class ConfigHelper
{
	/**
	 * storeRuntime
	 *
	 * @return  void
	 */
	public static function storeRuntime()
	{
		$file = PathHelper::getAdmin('com_schedule') . '/etc/runtime.yml';

		if (!is_file($file))
		{
			return;
		}

		$config = new Registry;

		$config->loadFile($file, 'yaml');

		$obj = new \stdClass;
		$obj->element = 'com_schedule';
		$obj->params = $config->toString('json');

		$db = \JFactory::getDbo();

		$db->updateObject('#__extensions', $obj, 'element');
	}

	/**
	 * getParams
	 *
	 * @return  \JRegistry
	 */
	public static function getParams()
	{
		return Config::getConfig();
	}
}
