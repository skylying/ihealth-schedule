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
use Windwalker\Helper\XmlHelper;

/**
 * Class JFormFieldDateTimePicker
 *
 * XML properties:
 * - show_button: (optional) is whether to show calendar button (true / false).
 *                If set to false will hide calendar button.
 *                Else set to true will show calendar button.
 *                Default is false.
 * - date_format: (optional) Setup datetimepicker's date format. Default is "YYYY-MM-DD".
 * - width:       (optional) Setup input width (in px).
 * - readonly:    (optional) The field cannot be changed and will automatically inherit the default value.
 * - disabled:    (optional) is whether the text box is disabled (true or false).
 *                If the text box is disabled, the date cannot be changed, selected or copied.
 * - hint:        (optional) is placeholder attribute.
 * - required:    (optional) The field must be filled before submitting the form.
 */
class JFormFieldDateTimePicker extends JFormField
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

		$id         = $this->id;
		$showButton = XmlHelper::getBool($this->element, 'show_button', false);
		$readonly   = XmlHelper::getBool($this->element, 'readonly', false);
		$disabled   = XmlHelper::getBool($this->element, 'disabled', false);
		$hint       = XmlHelper::get($this->element, 'hint', '');
		$required   = XmlHelper::get($this->element, 'required', '');
		$dateFormat = ' data-date-format="' . XmlHelper::get($this->element, 'date_format', 'YYYY-MM-DD') . '"';

		$readonly   = $readonly ? ' readonly' : '';
		$disabled   = $disabled ? ' disabled' : '';
		$hint       = empty($hint) ? '' : ' placeholder="' . $hint . '"';
		$required   = empty($required) ? '' : ' required="required"';
		$inputClass = 'form-control';
		$style      = $this->getStyle();

		if (false === $showButton)
		{
			$inputClass .= ' ' . $this->class;
			$dateFormat = '';
		}

		$input = '<input type="text" name="' . $this->name . '" id="' . $this->id . '" class="' . $inputClass . '"' .
			' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
			$readonly . $disabled . $hint . $required . $dateFormat . ' />';

		if (true === $showButton)
		{
			$class = $this->class;
			$id .= '_container';

			$this->class .= ' form-control';

			$html = <<<HTML
<div class='input-group date {$class}' id='{$id}' style="{$style}" {$dateFormat}>
	{$input}
	<span class="input-group-addon">
		<span class="glyphicon glyphicon-calendar"></span>
	</span>
</div>
HTML;
		}
		else
		{
			$html = <<<HTML
<div style="{$style}">
	{$input}
</div>
HTML;
		}

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

		$width = (int) XmlHelper::get($this->element, 'width');
		$width = ($width > 0 ? $width : 130);

		$styles[] = 'width:' . $width . 'px;';

		return implode(' ', $styles);
	}
}
