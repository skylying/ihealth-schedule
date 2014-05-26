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
 * Class ScheduleComponent
 *
 * @since 1.0
 */
final class ScheduleComponent extends \Schedule\Component\ScheduleComponent
{
	/**
	 * Property defaultController.
	 *
	 * @var string
	 */
	protected $defaultController = 'member.display';

	/**
	 * init
	 *
	 * @return void
	 */
	protected function prepare()
	{
		parent::prepare();

		\Windwalker\Api\Response\JsonResponse::registerErrorHandler();
	}
}
