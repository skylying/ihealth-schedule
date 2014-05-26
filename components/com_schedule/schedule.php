<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';

JLoader::registerPrefix('Schedule', JPATH_COMPONENT);
JLoader::registerNamespace('Schedule', JPATH_COMPONENT_ADMINISTRATOR . '/src');
JLoader::registerNamespace('Windwalker', JPATH_COMPONENT_ADMINISTRATOR . '/src');
JLoader::register('ScheduleComponent', JPATH_COMPONENT . '/component.php');

echo with(new ScheduleComponent)->execute();
