<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Schedule\Script;

// No direct access
defined('_JEXEC') or die;

use Windwalker\DI\Container;
use Schedule\Helper\AddressHelper;

/**
 * Class AddressScript
 *
 * - Usage 1: (PHP example)
 * <code>
 *     <?php
 *         Schedule\Script\AddressScript::bind('jform_city', 'jform_area');
 *     <?
 * </code>
 *
 * - Usage 2: (Javascript example)
 * <code>
 *     <?php
 *         // You must initialize script first
 *         Schedule\Script\AddressScript::init();
 *     ?>
 *
 *     jQuery(function($)
 *     {
 *         Address.bind('jform_city', 'jform_area', $('jform_area').val());
 *     });
 * </code>
 */
abstract class AddressScript
{
	/**
	 * Check if this helper is initialized or not.
	 *
	 * @var bool
	 */
	protected static $initialized = false;

	/**
	 * Property bindList.
	 *
	 * @var  array
	 */
	protected static $bindList = array();

	/**
	 * Bind events with city and area selection elements
	 *
	 * @param   string  $cityId  City selection element id
	 * @param   string  $areaId  Area selection element id
	 *
	 * @throws  \InvalidArgumentException
	 * @return  void
	 */
	public static function bind($cityId, $areaId)
	{
		if (empty($cityId) || empty($areaId))
		{
			throw new \InvalidArgumentException('city id and area id should not be empty.');
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
	 * Initialization
	 *
	 * @return  void
	 */
	public static function init()
	{
		if (true === self::$initialized)
		{
			return;
		}

		// Area options
		$areaOptions = AddressHelper::getAreaOptions();

		/** @var \Windwalker\Helper\AssetHelper $asset */
		$asset = Container::getInstance('com_schedule')->get('helper.asset');

		$asset->addJS('address.js');

		$asset->internalJS('
			jQuery(function()
			{
				Address.setAreas(' . json_encode($areaOptions) . ');
			});
		');

		self::$initialized = true;
	}
}
