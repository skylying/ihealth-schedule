<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

use Schedule\Table\Table;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Html\HtmlElement;

/**
 * Class RouteHelper
 *
 * @since 1.0
 */
class RouteHelper
{
	/**
	 * Property routeFields.
	 *
	 * @var  array
	 */
	protected static $routeFields = array(
		'id',
		'city',
		'city_title',
		'area',
		'area_title',
		'weekday',
		'sender_id',
		'sender_name'
	);

	/**
	 * Get view = overview table
	 *
	 * @param object $data
	 *
	 * @return \JGrid
	 */
	public static function getTable($data)
	{
		$grid = new \JGrid;

		$weekList = array('sender', 'MON', 'TUE', 'WED', 'THU', 'FRI');
		$senderList = self::getSenders();
		$routeData = self::getSortedData($data);

		$option['class'] = 'table table-bordered';
		$option['style'] = 'margin:0 auto;';

		$grid->setTableOptions($option);
		$grid->setColumns($weekList);

		// Print head row
		$grid->addRow(array('class' => 'headRow'), 1);

		foreach ($weekList as $weekDay)
		{
			if ($weekDay == 'sender')
			{
				$grid->setRowCell($weekDay, '', array('style' => 'width:5%'));

				continue;
			}

			$grid->setRowCell($weekDay, $weekDay, array('style' => 'width:19%'));
		}

		// Print body row
		foreach ($senderList as $sender)
		{
			$grid->addRow();

			foreach ($weekList as $weekDay)
			{
				// Print first column
				if ($weekDay == 'sender')
				{
					$grid->setRowCell($weekDay, $sender);

					continue;
				}

				if (isset($routeData[$sender][$weekDay]))
				{
					// Render html elements with route info inside
					$cellContent = self::getRouteStyle((array) $routeData[$sender][$weekDay]);

					$grid->setRowCell($weekDay, $cellContent, array('style' => 'position:relative'));
				}
				else
				{
					$grid->setRowCell($weekDay, '-');
				}
			}
		}

		return $grid;
	}

	/**
	 * Get sender name list
	 *
	 * @return  array
	 */
	private static function getSenders()
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('name')
			->from(TABLE::SENDERS);

		return $db->setQuery($query)->loadColumn();
	}

	/**
	 * Sort data in order of [外送藥師][外送日][路線 id] = array('路線名稱', '路線種類')
	 *
	 * @param array $data
	 *
	 * @return  array
	 */
	private static function getSortedData($data)
	{
		$result = [];

		// Prepare institute mapper
		$instituteMapper = new DataMapper(Table::INSTITUTES);

		$fields = self::$routeFields;

		foreach ($data as $key => $value)
		{
			// Get institute short title
			$instituteData = $instituteMapper->findOne(array("id" => $value->institute_id));

			// Different type has different route name
			if ($value->type == 'customer')
			{
				$title = \JString::substr($value->city_title, 0, 2) . \JString::substr($value->area_title, 0, 2) . '散客';
			}
			else
			{
				$title = $instituteData->short_title;
			}

			$name = $value->sender_name;
			$weekDay = $value->weekday;

			$result[$name][$weekDay][$value->route_id] = array(
				'title' => $title,
				'type' => $value->type,
				'institute_id' => $value->institute_id,
			);
		}

		return $result;
	}

	/**
	 * Pack route information with html element, will render them on table
	 *
	 * Template look like :
	 *
	 * <div class="route-outer">
	 * 	<div>
	 * 		<input />
	 * 		<label>
	 * 	</div>
	 * </div>
	 *
	 * @param array $aliasArray
	 *
	 * @return  string
	 */
	private static function getRouteStyle(array $aliasArray)
	{
		// Creat selectall and its mask
		$html =	<<<HTML
<span class="mask">
	<span class="batchbutton checkall glyphicon glyphicon-ok-sign"></span>
	<span class="batchbutton uncheckall glyphicon glyphicon-remove-sign"></span>
</span>
HTML;

		foreach ($aliasArray as $key => $value)
		{
			// Prepare input value we need
			$jsonValue = json_encode($value);

			// Saparate different route type
			$bgColor = ($value['type'] == 'customer') ? 'route-outer customer-bg' : 'route-outer institute-bg';

			// Create <input>
			$inputAttr = array('id' => $key, 'type' => 'checkbox', 'class' => 'routeinput', "value" => htmlspecialchars($jsonValue));
			$input = new HtmlElement('input', '', $inputAttr);

			// Create <label>
			$labelAttr = array('for' => $key);
			$label = new HtmlElement('label', $value['title'], $labelAttr);

			// Create inner <div>, and insert <label> & <input>
			$innerDiv = new HtmlElement('div', $input . $label);

			// Create outer <div>, and insert inner <div>
			$outerDivAttr = array('class' => $bgColor);
			$outerDiv = new HtmlElement('div', $innerDiv, $outerDivAttr);

			$html .= (string) ' ' . $outerDiv;
		}

		return $html;
	}
}
