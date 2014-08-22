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
use Windwalker\Html\HtmlElement;

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
 * - dpBindEvent: (optional) Function name to be triggered when element is changed.
 *                Function format: function($node) { ... }
 *                - $node is the current datetimepicker element
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
	 * Property width.
	 *
	 * @var  int
	 */
	protected $width = 130;

	/**
	 * Property show_button.
	 *
	 * @var  bool
	 */
	protected $show_button = false;

	/**
	 * Property date_format.
	 *
	 * @var  string
	 */
	protected $date_format = 'YYYY-MM-DD';

	/**
	 * Property dpBindEvent.
	 *
	 * @var  string
	 */
	protected $dpBindEvent = '';

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement $element
	 * @param   mixed            $value
	 * @param   null             $group
	 *
	 * @return  boolean  True on success.
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		$width = (int) XmlHelper::get($this->element, 'width');
		$dpBindEvent = XmlHelper::get($this->element, 'dpBindEvent', '');

		$this->width       = ($width > 0 ? $width : 130);
		$this->show_button = XmlHelper::getBool($this->element, 'show_button', false);
		$this->date_format = XmlHelper::get($this->element, 'date_format', 'YYYY-MM-DD');
		$this->dpBindEvent = empty($dpBindEvent) ? '' : $dpBindEvent . '(node);';

		return $return;
	}

	/**
	 * getInput
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		$this->init();

		$id          = $this->id;
		$showButton  = $this->show_button;
		$style       = $this->getStyle();
		$dateFormat  = $this->date_format;
		$dpBindEvent = $this->dpBindEvent;
		$value       = ('0000-00-00' === $this->value) ? '' : $this->value;

		$attr = array(
			'type'             => 'text',
			'name'             => $this->name,
			'id'               => $this->id . (false === $showButton ? '' : '_input'),
			'value'            => $value,
			'style'            => $style,
			'class'            => 'form-control ' . (false === $showButton ? $this->class : ''),
			'readonly'         => $this->readonly,
			'disabled'         => $this->disabled,
			'placeholder'      => $this->hint,
			'required'         => $this->required,
			'data-date-format' => (false === $showButton ? $dateFormat : ''),
		);

		$input = (string) new HtmlElement('input', '', $attr);

		if (true === $showButton)
		{
			$class = $this->class;

			$html = <<<HTML
<div class='input-group date {$class}' id='{$id}' style="{$style}" data-date-format="{$dateFormat}">
	{$input}
	<span class="input-group-addon">
		<span class="glyphicon glyphicon-calendar"></span>
	</span>
</div>
HTML;
		}
		else
		{
			$html = $input;
		}

		// Do not load datetimepicker when readonly or disabled
		if (! $this->readonly && ! $this->disabled)
		{
			$js = <<<JAVASCRIPT
jQuery(function ($)
{
	var node = $('#{$id}');

	node.datetimepicker({
		pickTime: false
	});

	{$dpBindEvent}
});
JAVASCRIPT;

			JFactory::getDocument()->addScriptDeclaration($js);
		}

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

		JHtmlBootstrap::framework();

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

		$styles[] = 'width:' . $this->width . 'px;';

		return implode(' ', $styles);
	}
}
