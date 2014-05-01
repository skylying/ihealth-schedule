<?php

/**
 * Class JFormFieldSelect2
 *
 */
class JFormFieldSelect2 extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'select2';

	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JUri::root(true) . '/media/com_schedule/library/select2/select2.css');
		$doc->addScript(JUri::root(true) . '/media/com_schedule/library/select2/select2.js');

		$script = '
			(function($) {
				$(document).ready(function() {
					var $node = $("#' . $this->id . '");

					$node.select2({
						minimumInputLength: "' . $this->element['minimumInputLength'] . '",
						placeholder : "' . $this->element['hint'] . '",
						ajax:
						{
							url: "' . JRoute::_('index.php?option=com_schedule&task=institutes.search.json', false) . '",
							dataType: "json",
							data: function(term)
							{
								return {"filter_search" : term};
							},
							results : function(data)
							{
								return {results : data};
							}
						},
						formatResult : function(result)
						{
							return  result.title;
						},
						formatSelection : function(result)
						{
							return result.title;
						},
						dropdownCssClass: "bigdrop",
						escapeMarkup: function (m) { return m; },
					});
				});
			})(jQuery);
		';

		$doc->addScriptDeclaration($script);

		$html = array();

		$html[] = '<input';
		$html[] = ' id="' . $this->id . '"';
		$html[] = ' name="' . $this->name . '"';
		$html[] = ' class="' . $this->class . '"';
		$html[] = ' />';

		return implode('', $html);
	}
}
