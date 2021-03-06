<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Controller\Resolver\ControllerDelegator;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleControllerAddressesDelegator
 *
 * @since 1.0
 */
class ScheduleControllerAddressesDelegator extends ControllerDelegator
{
	/**
	 * registerAliases
	 *
	 * @return  void
	 */
	protected function registerAliases()
	{
	}

	/**
	 * createController
	 *
	 * @param string $class
	 *
	 * @return  \Windwalker\Controller\Controller
	 */
	protected function createController($class)
	{
		return parent::createController($class);
	}
}
