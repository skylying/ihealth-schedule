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
			$url = 'index.php?option=com_schedule&task=member.edit.edit&id=' . $item->member_id;
			$text = '<span class="glyphicon glyphicon-user"></span> ' .
				$item->member_name .
				' <span class="glyphicon glyphicon-share-alt"></span>';

			return \JHtml::link($url, $text, $attr);
		}

		if ('resident' === $item->type
			|| ('resident' !== $item->type && $item->institute_id > 0))
		{
			$url = 'index.php?option=com_schedule&task=institute.edit.edit&id=' . $item->institute_id;
			$text = '<span class="glyphicon glyphicon-home"></span> ' .
				$item->institute_title .
				' <span class="glyphicon glyphicon-share-alt"></span>';

			return \JHtml::link($url, $text, $attr);
		}

		return '';
	}
}
