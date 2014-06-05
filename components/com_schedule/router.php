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
	$input = \JFactory::getApplication()->input;

	// Strip "/api"
	array_shift($segments);

	$query = array();

	// Set class (View or Controller)
	if (! empty($segments[0]))
	{
		$method = $input->get('_method', $input->getMethod());
		$method = strtolower($method);

		// Prepare RESTful
		if ('get' == $method)
		{
			$query['view'] = $segments[0];
		}
		elseif ('post' == $method || 'put' == $method)
		{
			$query['task'] = $segments[0] . '.edit.save';
		}
		elseif ('delete' == $method)
		{
			$query['task'] = $segments[0] . '.state.delete';
		}
	}

	// Set id if exists
	if (! empty($segments[1]))
	{
		$query['id'] = $segments[1];
	}

	if (isset($query['view']) && 'holidays' === $query['view'])
	{
		if (! empty($segments[1]) && ! empty($segments[2]))
		{
			$query['start'] = str_replace(':', '-', $segments[1]);
			$query['end'] = str_replace(':', '-', $segments[2]);
		}
	}

	return $query;
}
