<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleHelper
 *
 * @since 1.0
 */
abstract class ScheduleHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		$app       = \JFactory::getApplication();
		$inflector = \JStringInflector::getInstance(true);

		$menus = array(
			'institutes' => 'home',
			'prescriptions' => 'list-alt',
			'schedules' => 'calendar',
			'tasks' => 'tasks',
			'senders' => 'envelope',
			'members' => 'user',
			'customers' => 'tree-deciduous',
			'hospitals' => 'tower',
			'holidays' => 'calendar',
			'routes' => 'road',
		);

		$noMvcMenus = array(
			'drugs' => 'tint',
			'addresses' => 'list-alt',
			'drugprices' => 'usd',
			'colors' => 'th-large',
			'images' => 'picture',
		);

		if (JDEBUG)
		{
			$menus = array_merge($menus, $noMvcMenus);
		}

		foreach ($menus as $folder => $icon)
		{
			// if ($folder->isDir() && $inflector->isPlural($view = $folder->getBasename()))
			{
				JHtmlSidebar::addEntry(
					'<i class="glyphicon glyphicon-' . $icon . '"></i> ' .
					JText::sprintf(sprintf('COM_SCHEDULE_%s_TITLE_LIST', strtoupper($folder))),
					'index.php?option=com_schedule&view=' . $folder,
					($vName == $folder)
				);
			}
		}

		$dispatcher = \JEventDispatcher::getInstance();
		$dispatcher->trigger('onAfterAddSubmenu', array('com_schedule', $vName));
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string  $option  Action option.
	 *
	 * @return  JObject
	 *
	 * @since   1.0
	 */
	public static function getActions($option = 'com_schedule')
	{
		$user   = JFactory::getUser();
		$result = new \JObject;

		$actions = array(
			'core.admin',
			'core.manage',
			'core.create',
			'core.edit',
			'core.edit.own',
			'core.edit.state',
			'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $option));
		}

		return $result;
	}

	/**
	 * getColorBlock
	 * ex: getColorBlock('#ff0000', 30)            => generate a 30px red square
	 * ex: getColorBlock('red', 20, 'pull-right')  => generate a 20px pull-right red square
	 *
	 * @param string $color
	 * @param int    $size
	 * @param string $class
	 *
	 * @return  string
	 */
	public static function getColorBlock($color = '#eee', $size = 25, $class = null)
	{
		$attributes = array(
			'class' => $class,
			'style' => sprintf('width:%spx; height:%spx; background:%s; margin:0 auto', $size, $size, $color)
		);

		return (string) new \Windwalker\Html\HtmlElement('div', 'content', $attributes);
	}
}
