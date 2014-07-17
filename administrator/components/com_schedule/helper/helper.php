<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
use Windwalker\Html\HtmlElement;
use Schedule\Helper\SenderHelper;

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
		$menus = array(
			'rxresidents'   => 'list-alt',
			'rxindividuals' => 'list-alt',
			'schedules'     => 'calendar',
			'tasks'         => 'tasks',
			'members'       => 'user',
			'customers'     => 'tree-deciduous',
			'institutes'    => 'home',
			'senders'       => 'envelope',
			'hospitals'     => 'tower',
			'holidays'      => 'calendar'
		);

		$menusForSender = array(
			'tasks' => 'tasks',
		);

		$noMvcMenus = array(
			'routes'     => 'road',
			'drugs'      => 'tint',
			'addresses'  => 'list-alt',
			'drugprices' => 'usd',
			'colors'     => 'th-large',
			'images'     => 'picture',
		);

		// Show non-UI link if in debug mode.
		if (JDEBUG)
		{
			$menus = array_merge($menus, $noMvcMenus);
		}

		if (SenderHelper::isSenderLogin())
		{
			$menus = $menusForSender;
		}

		foreach ($menus as $folder => $icon)
		{
			$iconSpan = new HtmlElement('span', null, ['class' => 'glyphicon glyphicon-' . $icon]);

			JHtmlSidebar::addEntry(
				$iconSpan . ' ' . JText::sprintf(sprintf('COM_SCHEDULE_%s_TITLE_LIST', strtoupper($folder))),
				'index.php?option=com_schedule&view=' . $folder,
				($vName == $folder)
			);
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
}
