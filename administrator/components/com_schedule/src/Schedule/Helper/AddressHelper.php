<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Schedule\Helper;

// No direct access
defined('_JEXEC') or die;

use Schedule\Table\Table;

/**
 * Class AddressHelper
 */
abstract class AddressHelper
{
	/**
	 * Check if this helper is initialized or not.
	 *
	 * @var bool
	 */
	protected static $initialized = false;

	/**
	 * Property address.
	 *
	 * @var  array
	 */
	protected static $addresses = array();

	/**
	 * Property areas.
	 *
	 * @var  array
	 */
	protected static $areas = array();

	/**
	 * Property areaOptions.
	 *
	 * @var  array
	 */
	protected static $areaOptions = array();

	/**
	 * Get city HTML selection list
	 *
	 * @param   string   $name       The value of the HTML name attribute.
	 * @param   mixed    $attribs    Additional HTML attributes for the <select> tag. This
	 *                               can be an array of attributes, or an array of options. Treated as options
	 *                               if it is the last argument passed. Valid options are:
	 *                               Format options, see {@see JHtml::$formatOptions}.
	 *                               Selection options, see {@see JHtmlSelect::options()}.
	 *                               list.attr, string|array: Additional attributes for the select
	 *                               element.
	 *                               id, string: Value to use as the select element id attribute.
	 *                               Defaults to the same as the name.
	 *                               list.select, string|array: Identifies one or more option elements
	 *                               to be selected, based on the option key values.
	 * @param   string   $optKey     The name of the object variable for the option value. If
	 *                               set to null, the index of the value array is used.
	 * @param   string   $optText    The name of the object variable for the option text.
	 * @param   mixed    $selected   The key that is selected (accepts an array or a string).
	 * @param   mixed    $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True to translate
	 *
	 * @return  string HTML for the select list.
	 */
	public static function getCityList($name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false,
		$translate = false)
	{
		static::init();

		return \JHtmlSelect::genericlist(static::$addresses, $name, $attribs, $optKey, $optText, $selected, $idtag, $translate);
	}

	/**
	 * Get area HTML selection list
	 *
	 * @param   string   $cityId     City id
	 * @param   string   $name       The value of the HTML name attribute.
	 * @param   mixed    $attribs    Additional HTML attributes for the <select> tag. This
	 *                               can be an array of attributes, or an array of options. Treated as options
	 *                               if it is the last argument passed. Valid options are:
	 *                               Format options, see {@see JHtml::$formatOptions}.
	 *                               Selection options, see {@see JHtmlSelect::options()}.
	 *                               list.attr, string|array: Additional attributes for the select
	 *                               element.
	 *                               id, string: Value to use as the select element id attribute.
	 *                               Defaults to the same as the name.
	 *                               list.select, string|array: Identifies one or more option elements
	 *                               to be selected, based on the option key values.
	 * @param   string   $optKey     The name of the object variable for the option value. If
	 *                               set to null, the index of the value array is used.
	 * @param   string   $optText    The name of the object variable for the option text.
	 * @param   mixed    $selected   The key that is selected (accepts an array or a string).
	 * @param   mixed    $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True to translate
	 *
	 * @return  string HTML for the select list.
	 */
	public static function getAreaList($cityId, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false,
		$translate = false)
	{
		static::init();

		if (! isset(static::$addresses[$cityId]))
		{
			return '';
		}

		return \JHtmlSelect::genericlist(static::$addresses[$cityId]['areas'], $name, $attribs, $optKey, $optText, $selected, $idtag, $translate);
	}

	/**
	 * getCities
	 *
	 * @return  array
	 */
	public static function getCities()
	{
		static::init();

		return static::$addresses;
	}

	/**
	 * getAreas
	 *
	 * @return  array
	 */
	public static function getAreas()
	{
		static::init();

		return static::$areas;
	}

	/**
	 * getAreaOptions
	 *
	 * @return  array
	 */
	public static function getAreaOptions()
	{
		static::init();

		return static::$areaOptions;
	}

	/**
	 * Initialization
	 *
	 * @return  void
	 */
	protected static function init()
	{
		if (true === self::$initialized)
		{
			return;
		}

		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);
		$address = array();

		$query->select('id, title')
			->from(Table::CITIES)
			->where('published=1');

		foreach ($db->setQuery($query)->loadObjectList() as $city)
		{
			if (! isset($address[$city->id]))
			{
				$address[$city->id] = array(
					'value' => $city->id,
					'text' => $city->title,
					'areas' => array(),
				);
			}
		}

		$query->clear()
			->select('id, title, city_id')
			->from(Table::AREAS)
			->where('published=1');

		foreach ($db->setQuery($query)->loadObjectList() as $area)
		{
			if (! isset($address[$area->city_id]))
			{
				continue;
			}

			if (! isset($address[$area->city_id]['areas'][$area->id]))
			{
				$address[$area->city_id]['areas'][$area->id] = array(
					'value' => $area->id,
					'text' => $area->title,
				);
			}
		}

		static::$addresses = $address;

		foreach (static::$addresses as $cityId => $address)
		{
			static::$areas[$cityId] = $address['areas'];

			static::$areaOptions[$cityId] = \JHtmlSelect::options($address['areas']);
		}

		self::$initialized = true;
	}
}
