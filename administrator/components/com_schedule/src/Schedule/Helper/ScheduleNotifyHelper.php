<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

/**
 * Class ScheduleNotifyHelper
 */
class ScheduleNotifyHelper
{
	const SHOULD_COMBINE_SCHEDULES = 1;

	/**
	 * text
	 *
	 * @param int $condition Notification condition
	 *
	 * @return  string
	 */
	public static function text($condition)
	{
		switch ((int) $condition)
		{
			case self::SHOULD_COMBINE_SCHEDULES:
				return '的排程需要被合併';
		}

		return '';
	}
}
