<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Schedule\Config;

use Windwalker\Helper\PathHelper;
use Windwalker\System\Config\AbstractConfig;

// No direct access
defined('_JEXEC') or die;

/**
 * Class Config
 *
 * @since 1.0
 */
abstract class Config extends AbstractConfig
{
	/**
	 * Property type.
	 *
	 * @var  string
	 */
	protected static $type = 'yaml';

	/**
	 * getPath
	 *
	 * @return  string
	 */
	public static function getPath()
	{
		$type = static::$type;
		$ext  = (static::$type == 'yaml') ? 'yml' : $type;

		$path = PathHelper::getAdmin('com_schedule') . '/etc/runtime.' . $ext;

		if (!is_file($path))
		{
			$path = PathHelper::getAdmin('com_schedule') . '/etc/config.' . $ext;
		}

		return $path;
	}
}
