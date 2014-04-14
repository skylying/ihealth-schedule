<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Schedule\Provider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ServiceProvider
 *
 * @since 1.0
 */
class ScheduleProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container $container The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 */
	public function register(Container $container)
	{
	}
}
