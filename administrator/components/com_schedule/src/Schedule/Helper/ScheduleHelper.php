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
				$text = $item->member_name . ' <i class="glyphicon glyphicon-share-alt"></i>';

				return \JHtml::link($url, $text, $attr);
			case 'resident':
				$url = 'index.php?option=com_schedule&task=institute.edit.edit&id=' . $item->institute_id;
				$text = $item->institute_title . ' <i class="glyphicon glyphicon-share-alt"></i>';

				return \JHtml::link($url, $text, $attr);
		}

		return '';
	}

	/**
	 * Get prescription edit link
	 *
	 * @param   Data $item
	 *
	 * @return  string
	 */
	public static function getRXLink(Data $item)
	{
		if ((int) $item->rx_id <= 0)
		{
			return '';
		}

		$attr = array('target' => '_blank');
		$text = $item->rx_id . ' <i class="glyphicon glyphicon-share-alt"></i>';

		switch ($item->type)
		{
			case 'individual':
				$url = 'index.php?option=com_schedule&task=rxindividual.edit.edit&id=' . $item->rx_id;

				return \JHtml::link($url, $text, $attr);
			case 'resident':
				$url = 'index.php?option=com_schedule&task=rxresident.edit.edit&id=' . $item->rx_id;

				return \JHtml::link($url, $text, $attr);
		}

		return '';
	}
}
