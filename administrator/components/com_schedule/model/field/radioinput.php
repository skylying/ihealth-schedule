<?php

use Windwalker\Html\HtmlElement;

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
	 * getInput
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$class        = !empty($this->class) ? ' class="' . $this->class . '"' : '';

		// Initialize JavaScript field attributes.
		$onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		// Including fallback code for HTML5 non supported browsers.
		JHtml::_('jquery.framework');

		// Invisible json telephone input
		$html[] = '<input type="text" style="display:none;" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class
			. $onchange . ' />';

		$jsonString = json_decode($this->value);

		// Use loops to load json data to into empty html input
		for ($key = 0; $key <= 2; $key++)
		{
			// Get radio input html
			$inputAttr = array('id' => $key, 'type' => 'radio', 'name' => "radioInput" . $this->id);
			$Unchecked = new HtmlElement('input', '', $inputAttr);

			if (empty($jsonString))
			{
				$html[] = '<div class="form-group"> 預設 ' . $Unchecked .
				' <input type="text" id="' . $this->id . $key . '"
				name="textInput' . $this->id . '" value="' . '"' . $class
				. $onchange . ' /></div>';
			}
		}

		// Use loops to load html if the json data is not empty
		foreach ((array) $jsonString as $key => $value)
		{
			// Get radio input html
			$inputAttr = array('id' => $key, 'type' => 'radio', 'checked' => 'checked', 'name' => "radioInput" . $this->id);
			$Checked = new HtmlElement('input', '', $inputAttr);

			$inputAttr = array('id' => $key, 'type' => 'radio', 'name' => "radioInput" . $this->id);
			$Unchecked = new HtmlElement('input', '', $inputAttr);

			$default = $value->default;

			$checkbox = $Unchecked;

			// Check if the checkbox is checked
			if ($default == '1')
			{
				$checkbox = $Checked;
			}

			$html[] = '<div class="form-group"> 預設 ' . $checkbox . ' <input type="text" id="' . $this->id . $key . '"
				name="textInput' . $this->id . '" value="' . $value->number . '"' . $class
				. $onchange . ' /></div>';
		}

		return implode($html);
	}
}
