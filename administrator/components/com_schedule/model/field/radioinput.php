<?php
/**
 * Part of ihealth project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;

/**
 * Class JFormFieldRadioinput
 *
 * @since 1.0
 */
class JFormFieldRadioinput extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Radioinput';

	/**
	 * The allowable maxlength of the field.
	 *
	 * @var    integer
	 * @since  3.2
	 */
	protected $maxLength;

	/**
	 * The mode of input associated with the field.
	 *
	 * @var    mixed
	 * @since  3.2
	 */
	protected $inputmode;

	/**
	 * The name of the form field direction (ltr or rtl).
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $dirname;

	/**
	 * getInput
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Translate placeholder text
		$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

		// Initialize some field attributes.
		$size         = !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$maxLength    = !empty($this->maxLength) ? ' maxlength="' . $this->maxLength . '"' : '';
		$class        = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$readonly     = $this->readonly ? ' readonly' : '';
		$disabled     = $this->disabled ? ' disabled' : '';
		$required     = $this->required ? ' required aria-required="true"' : '';
		$hint         = $hint ? ' placeholder="' . $hint . '"' : '';
		$autocomplete = !$this->autocomplete ? ' autocomplete="off"' : ' autocomplete="' . $this->autocomplete . '"';
		$autocomplete = $autocomplete == ' autocomplete="on"' ? '' : $autocomplete;
		$autofocus    = $this->autofocus ? ' autofocus' : '';
		$spellcheck   = $this->spellcheck ? '' : ' spellcheck="false"';
		$pattern      = !empty($this->pattern) ? ' pattern="' . $this->pattern . '"' : '';
		$inputmode    = !empty($this->inputmode) ? ' inputmode="' . $this->inputmode . '"' : '';
		$dirname      = !empty($this->dirname) ? ' dirname="' . $this->dirname . '"' : '';
		$list         = '';

		// Initialize JavaScript field attributes.
		$onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		// Including fallback code for HTML5 non supported browsers.
		JHtml::_('jquery.framework');
		JHtml::_('script', 'system/html5fallback.js', false, true);

		if (!empty($options))
		{
			$html[] = JHtml::_('select.suggestionlist', $options, 'value', 'text', $this->id . '_datalist"');
			$list   = ' list="' . $this->id . '_datalist"';
		}

		$html[] = '<input type="text" style="display:none;" name="' . $this->name . '" id="' . $this->id . '"' . $dirname . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $list
			. $hint . $onchange . $maxLength . $required . $autocomplete . $autofocus . $spellcheck . $inputmode . $pattern . ' />';

		$jsonString = json_decode($this->value);

		for ($key = 0; $key <= 2; $key++)
		{
			if (empty($jsonString))
			{
				$html[] = ' 預設 <div style="margin:10px"><input type="radio"' . ' id="radio' . $this->id . $key . '"' . 'name="radioInput' . $this->id . '" />
				<input type="text"' . ' id="' . $this->id . $key . '"
				name="textInput' . $this->id . '" value="' . '"' . $class . $size . $disabled . $readonly . $list
					. $hint . $onchange . $maxLength . $required . $autocomplete . $autofocus . $spellcheck . $inputmode . $pattern . ' /></div>';
			}
		}

		foreach ((array) $jsonString as $key => $value)
		{
			$jsonString = json_decode($this->value);

			$default = $value->default;

			if ($default == 'true')
			{
				$html[] = ' 預設 <div  style="margin:10px">
				<input type="radio"' . ' id="radio' . $this->id . $key . '"' . 'name="radioInput' . $this->id . '"checked />
				<input type="text"' . ' id="' . $this->id . $key . '"
				name="textInput' . $this->id . '" value="' . $value->number . '"' . $class . $size . $disabled . $readonly . $list
					. $hint . $onchange . $maxLength . $required . $autocomplete . $autofocus . $spellcheck . $inputmode . $pattern . ' />
					</div>';
			}
			else
			{
				$html[] = ' 預設 <div style="margin:10px"><input type="radio"' . ' id="radio' . $this->id . $key . '"' . 'name="radioInput' . $this->id . '" />
				<input type="text"' . ' id="' . $this->id . $key . '"
				name="textInput' . $this->id . '" value="' . $value->number . '"' . $class . $size . $disabled . $readonly . $list
					. $hint . $onchange . $maxLength . $required . $autocomplete . $autofocus . $spellcheck . $inputmode . $pattern . ' /></div>';
			}
		}

		return implode($html);

	}
}
