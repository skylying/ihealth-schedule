<?php

use \Windwalker\Html\HtmlElement;
use \Windwalker\Helper\XmlHelper;

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
 * XML Property list :
 *
 * - rows : total number of rows that user can use
 *  - EX : 3
 *
 * - label : The <legend> content
 *  - EX : 宅配電話 (Office)
 *
 *
 * HTML output :
 *
 * <input class="hide" name="jform[tel_office]">
 * <div class="visibleinput">
 * 	<span class="glyphicon glyphicon-ok"><span>
 * 	<input name="tel_office_input1" />
 *  ...
 *  ..
 * </div>
 *
 *  - The class="hide" input will be load and save as string with json format
 *  - The name="tel_office_input1" is where user can actually put phone numbers
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
		// Prepare html string
		$html = '';
		$hiddenInput = $this->renderHiddenInput();
		$visibleInputs = '';

		// Prepare empty object in case when no phone numbers exist (because we still need empty row)
		$emptyNumberSet = new stdClass;
		$emptyNumberSet->default = false;
		$emptyNumberSet->number = '';

		// Prepare XML params
		$params = $this->getParams();

		// Decode JSON string value
		if (is_string($this->value))
		{
			$this->value = json_decode($this->value);
		}

		// Prepare input data
		$numberSets = is_array($this->value) ? $this->value : array();

		// Start building HTML
		$html .= '<fieldset><legend>' . $params->label . '</legend>';
		$html .= $hiddenInput;

		for ($i = 0; $i < $params->rows; $i++)
		{
			if (empty($numberSets[$i]))
			{
				$numberSets[$i] = $emptyNumberSet;
			}

			// Prepare each input's attributes
			$spanClass = $numberSets[$i]->default ? 'default' : '';
			$default   = $numberSets[$i]->default ? 'true' : 'false';
			$number    = $numberSets[$i]->number ? $numberSets[$i]->number : '';

			$tmpHtmlString = <<<HTML
<div class="visibleinput">
	<span class="glyphicon glyphicon-ok {$spanClass}" title="{$default}" style="margin-right:20px"></span>
	<input type="text" class="{$params->shortName}_number" value="{$number}" placeholder="輸入電話號碼"/>
</div>
HTML;
			$visibleInputs .= $tmpHtmlString;
		}

		$html .= $visibleInputs . '</fieldset>';

		return $html;
	}

	/**
	 * Render hidden input with Windwalker HtmlElement
	 *
	 * @return  array
	 */
	public function renderHiddenInput()
	{
		$configure = array(
			'id'    => $this->id,
			'type'  => 'text',
			'name'  => $this->name,
			'value' => htmlspecialchars(json_encode($this->value), ENT_COMPAT, 'UTF-8'),
			'class' => 'hide hiddenjson',
		);

		$html = new HtmlElement('input', '', $configure);

		return (string) $html;
	}

	/**
	 * Get all xml params
	 *
	 * @return  stdClass
	 */
	public function getParams()
	{
		$params = new stdClass;

		// Take only "tel_office" instead of "jform[tel_office]" as shortName
		$params->shortName = XmlHelper::get($this->element, 'name');
		$params->rows      = XmlHelper::get($this->element, 'rows', 3);
		$params->label     = XmlHelper::get($this->element, 'label', '宅配電話');

		return $params;
	}
}
