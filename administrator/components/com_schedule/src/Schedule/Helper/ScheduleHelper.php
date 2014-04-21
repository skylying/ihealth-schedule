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

	/**
	 * getStatusSelector
	 *
	 * @param   string  $value
	 *
	 * @return  string
	 */
	public static function getStatusSelector($value)
	{
		$value = strtolower(trim($value));

		$options = array(
			'scheduled'		=> array('text' => '已排程',		'color' => '#26C281', 'icon' => 'calendar'),
			'emergency'		=> array('text' => '急件',		'color' => '#E74C3C', 'icon' => 'fire'),
			'deleted'		=> array('text' => '已刪除',		'color' => '#b97e7e', 'icon' => 'trash'),
			'cancelonly'	=> array('text' => '取消-要退單',	'color' => '#95A5A6', 'icon' => 'remove'),
			'cancelreject'	=> array('text' => '取消-不退單',	'color' => '#26C281', 'icon' => 'remove'),
			'pause'			=> array('text' => '暫緩',		'color' => '#F5AB35', 'icon' => 'pause'),
		);
		$text = '';
		$color = '';
		$icon = '';

		if (isset($options[$value]))
		{
			$text = $options[$value]['text'];
			$color = $options[$value]['color'];
			$icon = $options[$value]['icon'];
		}

		$html[] = '<div class="btn-group">';
		$html[] = '    <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" style="background:' . $color . ';">';
		$html[] = '        <i class="glyphicon glyphicon-' . $icon . '"></i> ' . $text . ' <span class="caret"></span>';
		$html[] = '    </button>';
		$html[] = '    <ul class="dropdown-menu">';

		foreach ($options as $key => $option)
		{
			if ($value === $key || 'deleted' === $key)
			{
				continue;
			}

			$html[] = '<li style="background:' . $option['color'] . ';" data-status="' . $key . '">';
			$html[] = '    <a href="#"><i class="glyphicon glyphicon-' . $option['icon'] . '"></i> ' . $option['text'] . ' </a>';
			$html[] = '</li>';
		}

		$html[] = '</ul></div>';

		return implode("\n", $html);
	}
}
