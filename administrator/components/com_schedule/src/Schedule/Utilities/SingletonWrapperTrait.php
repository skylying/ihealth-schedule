<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Utilities;

use Windwalker\Helper\ReflectionHelper;

/**
 * Trait SingletonWrapperTrait
 *
 * @since 1.0
 */
trait SingletonWrapperTrait
{
	/**
	 * Property instance.
	 *
	 * @var  null
	 */
	protected static $instance = null;


	/**
	 * getInstance
	 *
	 * @param string $class
	 *
	 * @return  object Self to return instance.
	 */
	public static function getInstance($class)
	{
		if (static::$instance)
		{
			return static::$instance;
		}

		$args = func_get_args();

		array_shift($args);

		/** @var $ref \ReflectionClass */
		$ref = ReflectionHelper::get($class);

		return static::$instance = $ref->newInstanceArgs($args);
	}
}
