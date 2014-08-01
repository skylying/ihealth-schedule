<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Component\ScheduleComponent as ScheduleComponentBase;
use Schedule\Helper\SenderHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleComponent
 *
 * @since 1.0
 */
final class ScheduleComponent extends ScheduleComponentBase
{
	/**
	 * Property defaultController.
	 *
	 * @var string
	 */
	protected $defaultController = 'rxresidents.display';

	/**
	 * init
	 *
	 * @return void
	 */
	public function prepare()
	{
		// If user belongs to sender, redirect to view = tasks
		$sender = SenderHelper::checkSender();
		$app = JFactory::getApplication();

		if ($sender && $app->input->get('view') !== 'tasks')
		{
			$app->redirect(JUri::root() . '/administrator/index.php?option=com_schedule&view=tasks');
		}

		parent::prepare();

		$asset = $this->container->get('helper.asset');

		$asset->addCss('global.css');
	}
}
