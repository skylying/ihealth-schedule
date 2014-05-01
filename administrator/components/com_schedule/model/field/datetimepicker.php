<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Windwalker\DI\Container;

/**
 * Class JFormFieldDateTimePicker
 */
class JFormFieldDateTimePicker extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'DateTimePicker';

	/**
	 * Check if this field type is initialized or not.
	 *
	 * @var bool
	 */
	protected static $initialized = false;

	/**
	 * getInput
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		$this->init();

		$id = $this->id;
		$style = $this->getStyle();

		$class = $this->class;

		// Set input class
		$this->class = ' form-control';
		$input = parent::getInput();

		$dateFormat = (string) $this->element['dateFormat'];
		$dateFormat = empty($dateFormat) ? 'YYYY-MM-DD' : $dateFormat;

		$html = <<<HTML
<div class='input-group date {$class}' id='{$id}' style="{$style}" data-date-format="{$dateFormat}">
	{$input}
	<span class="input-group-addon">
		<span class="glyphicon glyphicon-calendar"></span>
	</span>
</div>
HTML;

		$js = <<<JS
jQuery(function ()
{
	jQuery('#{$id}').datetimepicker({
		pickTime: false
	});
});
JS;

		JFactory::getDocument()->addScriptDeclaration($js);

		return $html;
	}

	/**
	 * Initialize
	 *
	 * @return  void
	 */
	protected function init()
	{
		if (true === self::$initialized)
		{
			return;
		}

		/** @var \Windwalker\Helper\AssetHelper $asset */
		$asset = Container::getInstance('com_schedule')->get('helper.asset');

		$asset->addCSS('bootstrap-datetimepicker.min.css');
		$asset->addJS('moment-with-langs.min.js');
		$asset->addJS('bootstrap-datetimepicker.min.js');

		self::$initialized = true;
	}

	/**
	 * getStyle
	 *
	 * @return  string
	 */
	protected function getStyle()
	{
		$styles = array();

		$width = (int) $this->element['width'];
		$width = ($width > 0 ? $width : 130);

		$styles[] = 'width:' . $width . 'px;';

		return implode(' ', $styles);
	}
}
