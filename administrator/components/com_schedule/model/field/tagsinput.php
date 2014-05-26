<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Class JFormFieldTagsInput
 */
class JFormFieldTagsInput extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	public $type = 'TagsInput';

	/**
	 * Method to get the field input for a tag field.
	 *
	 * @return  string  The field input.
	 *
	 * @since   3.1
	 */
	protected function getInput()
	{
		// Get the field id
		$id    = isset($this->element['id']) ? $this->element['id'] : null;
		$cssId = '#' . $this->getId($id, $this->element['name']);

		$this->__set('multiple', 'true');
		$this->__set('name', $this->fieldname);

		JHtmlFormbehavior::chosen($cssId);

		JFactory::getDocument()->addScriptDeclaration("
			(function($){
				$(document).ready(function () {
					// Method to add tags pressing enter
					$('" . $cssId . "_chzn input').keyup(function(event) {

						// Tag is greater than 3 chars and enter pressed
						if (this.value.length >= 2 && (event.which === 13 || event.which === 188)) {

							// Search an highlighted result
							var highlighted = $('" . $cssId . "_chzn').find('li.active-result.highlighted').first();

							// Add the highlighted option
							if (event.which === 13 && highlighted.text() !== '')
							{
								// Extra check. If we have added a custom tag with this text remove it
								$('" . $cssId . " option').filter(function () { return $(this).val() == highlighted.text(); }).remove();

								// Select the highlighted result
								var tagOption = $('" . $cssId . " option').filter(function () { return $(this).html() == highlighted.text(); });
								tagOption.attr('selected', 'selected');
							}
							// Add the custom tag option
							else
							{
								var customTag = this.value;

								// Extra check. Search if the custom tag already exists (typed faster than AJAX ready)
								var tagOption = $('" . $cssId . " option').filter(function () { return $(this).html() == customTag; });
								if (tagOption.text() !== '')
								{
									tagOption.attr('selected', 'selected');
								}
								else
								{
									var option = $('<option>');
									option.text(this.value).val(this.value);
									option.attr('selected','selected');

									// Append the option an repopulate the chosen field
									$('" . $cssId . "').append(option);
								}
							}

							this.value = '';
							$('" . $cssId . "').trigger('liszt:updated');
							event.preventDefault();

						}
					});
				});
			})(jQuery);
			"
		);

		$input = parent::getInput();

		return $input;
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$options = array();

		foreach ($this->value as $value)
		{
			$options[] = JHtmlSelect::option($value, JText::alt(trim($value), true));
		}

		return $options;
	}
}
