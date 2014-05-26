<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Windwalker\Router\CmsRouter;
use Windwalker\Router\Helper\RoutingHelper;

include_once JPATH_ADMINISTRATOR . '/components/com_schedule/src/init.php';

// Prepare Router
$router = CmsRouter::getInstance('com_schedule');

// Register routing config and inject Router object into it.
$router = RoutingHelper::registerRouting($router, 'com_schedule');

/**
 * ScheduleBuildRoute
 *
 * @param   array  &$query
 *
 * @return  array
 */
function ScheduleBuildRoute(&$query)
{
	$segments = array();

	$router = CmsRouter::getInstance('com_schedule');

	$query = \Windwalker\Router\Route::build($query);

	if (!empty($query['view']))
	{
		$segments = $router->build($query['view'], $query);

		unset($query['view']);
	}

	return $segments;
}

/**
 * ScheduleParseRoute
 *
 * @param   array  $segments
 *
 * @return  array
 */
function ScheduleParseRoute($segments)
{
	$router = CmsRouter::getInstance('com_schedule');

	// Strip "/api"
	array_shift($segments);

	$segments = implode('/', $segments);

	// OK, let's fetch view name.
	$view = $router->getView(str_replace(':', '-', $segments));

	if ($view)
	{
		return array('view' => $view);
	}

	return array();
}
