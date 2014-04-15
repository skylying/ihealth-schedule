<?php

namespace Schedule\Helper;

/**
 * Class ScheduleHelper
 *
 * @since 1.0
 */
class ScheduleHelper
{
	/**
	 * getEditLink
	 *
	 * @param   object $item
	 *
	 * @return  string
	 */
	public static function getEditLink($item)
	{
		$attr = array('target' => '_blank');

		switch ($item->type)
		{
			case 'individual':
				$url = 'index.php?option=com_schedule&task=member.edit.edit&id=' . $item->member_id;

				return \JHtml::link($url, $item->member_name, $attr);
			case 'resident':
				$url = 'index.php?option=com_schedule&task=institute.edit.edit&id=' . $item->institute_id;

				return \JHtml::link($url, $item->institute_title, $attr);
		}

		return '';
	}
}
