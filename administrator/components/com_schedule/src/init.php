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

JLoader::registerPrefix('Schedule', JPATH_BASE . '/components/com_schedule');
JLoader::registerNamespace('Schedule', JPATH_ADMINISTRATOR . '/components/com_schedule/src');
JLoader::registerNamespace('Windwalker', __DIR__);
JLoader::register('ScheduleComponent', JPATH_BASE . '/components/com_schedule/component.php');
