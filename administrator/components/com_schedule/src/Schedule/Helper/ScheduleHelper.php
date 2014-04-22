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
				$text = '<i class="glyphicon glyphicon-user"></i> ' .
					$item->member_name .
					' <i class="glyphicon glyphicon-share-alt"></i>';

				return \JHtml::link($url, $text, $attr);
			case 'resident':
				$url = 'index.php?option=com_schedule&task=institute.edit.edit&id=' . $item->institute_id;
				$text = '<i class="glyphicon glyphicon-home"></i> ' .
					$item->institute_title .
					' <i class="glyphicon glyphicon-share-alt"></i>';

				return \JHtml::link($url, $text, $attr);
		}

		return '';
	}
}
