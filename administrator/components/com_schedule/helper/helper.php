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

		// Add Category Menu Item
		if ($app->isAdmin())
		{
			JHtmlSidebar::addEntry(
				JText::_('JCATEGORY'),
				'index.php?option=com_categories&extension=com_schedule',
				($vName == 'categories')
			);
		}

		foreach (new \DirectoryIterator(JPATH_ADMINISTRATOR . '/components/com_schedule/view') as $folder)
		{
			if ($folder->isDir() && $inflector->isPlural($view = $folder->getBasename()))
			{
				JHtmlSidebar::addEntry(
					JText::sprintf(sprintf('COM_SCHEDULE_%s_TITLE_LIST', strtoupper($folder))),
					'index.php?option=com_schedule&view=' . $view,
					($vName == $view)
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
}
