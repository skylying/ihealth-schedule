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

use Whoops\Example\Exception;
use Windwalker\DI\Container;
use Schedule\Table\Table;

/**
 * Class AddressHelper
 *
 * - Usage 1: (PHP example)
 * <code>
 *     <?php
 *         Schedule\Helper\AddressHelper::bind('jform_city', 'jform_area');
 *     <?
 * </code>
 *
 * - Usage 2: (Javascript example)
 * <code>
 *     <?php
 *         // You must initialize helper first
 *         Schedule\Helper\AddressHelper::init();
 *     ?>
 *
 *     jQuery(function($)
 *     {
 *         Address.bind('jform_city', 'jform_area', $('jform_area').val());
 *     });
 * </code>
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
	 * Property bindList.
	 *
	 * @var  array
	 */
	protected static $bindList = array();

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
	 * Bind events with city and area selection elements
	 *
	 * @param   string  $cityId  City selection element id
	 * @param   string  $areaId  Area selection element id
	 *
	 * @throws  \Whoops\Example\Exception
	 * @return  void
	 */
	public static function bind($cityId, $areaId)
	{
		if (empty($cityId) || empty($areaId))
		{
			throw new Exception('city id and area id should not be empty.');
		}

		$bindPair = $cityId . ':' . $areaId;

		if (isset(static::$bindList[$bindPair]))
		{
			return;
		}

		static::init();

		/** @var \Windwalker\Helper\AssetHelper $asset */
		$asset = Container::getInstance('com_schedule')->get('helper.asset');

		$js = <<<JS
jQuery(function($)
{
	Address.bind('{$cityId}', '{$areaId}', $('#{$areaId}').val());
});
JS;
		$asset->internalJS($js);

		static::$bindList[$bindPair] = true;
	}

	/**
	 * Initialize
	 *
	 * @return  void
	 */
	public static function init()
	{
		if (true === self::$initialized)
		{
			return;
		}

		static::initAddressData();

		// Area lists
		$areas = array();

		foreach (static::$addresses as $cityId => $address)
		{
			$areas[$cityId] = \JHtmlSelect::options($address['areas']);
		}

		/** @var \Windwalker\Helper\AssetHelper $asset */
		$asset = Container::getInstance('com_schedule')->get('helper.asset');

		$asset->addJS('address.js');

		$asset->internalJS('
			jQuery(function()
			{
				Address.setAreas(' . json_encode($areas) . ');
			});
		');

		self::$initialized = true;
	}

	/**
	 * getAddressData
	 *
	 * @return  array
	 */
	protected static function initAddressData()
	{
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
	}
}
