<?php

namespace Schedule\Helper;

use Windwalker\Data\Data;

/**
 * Class ScheduleHelper
 *
 * @since 1.0
 */
class ScheduleHelper
{
	/**
	 * getTargetLink
	 *
	 * @param   Data $item
	 *
	 * @return  string
	 */
	public static function getTargetLink(Data $item)
	{
		$attr = array('target' => '_blank');

		if ('individual' === $item->type
			|| ('individual' !== $item->type && $item->member_id > 0))
		{
			return \Schedule\Helper\UiHelper::foreignLink('member', $item->member_name, $item->member_id, '', $attr);
		}

		if ('resident' === $item->type
			|| ('resident' !== $item->type && $item->institute_id > 0))
		{
			return \Schedule\Helper\UiHelper::foreignLink('institute', $item->institute_title, $item->institute_id, '', $attr);
		}

		return '';
	}
}
