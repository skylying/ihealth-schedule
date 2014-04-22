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
	 * getEditLink
	 *
	 * @param   Data $item
	 *
	 * @return  string
	 */
	public static function getEditLink(Data $item)
	{
		$attr = array('target' => '_blank');

		switch ($item->type)
		{
			case 'individual':
				$url = 'index.php?option=com_schedule&task=member.edit.edit&id=' . $item->member_id;
				$text = '<span class="glyphicon glyphicon-user"></span> ' .
					$item->member_name .
					' <span class="glyphicon glyphicon-share-alt"></span>';

				return \JHtml::link($url, $text, $attr);
			case 'resident':
				$url = 'index.php?option=com_schedule&task=institute.edit.edit&id=' . $item->institute_id;
				$text = '<span class="glyphicon glyphicon-home"></span> ' .
					$item->institute_title .
					' <span class="glyphicon glyphicon-share-alt"></span>';

				return \JHtml::link($url, $text, $attr);
		}

		return '';
	}
}
