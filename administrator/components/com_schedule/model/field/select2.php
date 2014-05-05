<?php

use \Windwalker\DI\Container;

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
	 * Check if this field type is initialized or not.
	 *
	 * @var bool
	 */
	protected static $initialized = false;

	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		/** @var \Windwalker\Helper\AssetHelper $asset */
		$asset = Container::getInstance('com_schedule')->get('helper.asset');

		// Check if js/css library are included or not
		$this->init();

		$consoleResult = (is_null($this->element['apiConsoleResult'])) ? "false" : $this->element['apiConsoleResult'];
		$apiDataType   = (is_null($this->element['apiDataType'])) ? "json" : $this->element['apiDataType'];

		$script = '
			(function($) {
				$(document).ready(function(){

					var $node = $("#' . $this->id . '"),
						consoleResult = ' . $consoleResult . ';

					$node.select2({
						minimumInputLength: "' . $this->element['minimumInputLength'] . '",
						placeholder : "' . $this->element['hint'] . '",
						ajax: {
							url: ' . json_encode(JRoute::_($this->element['apiUrl'], false)) . ',
							dataType: "' . $apiDataType . '",
							data: function(term)
							{
								return {"' . $this->element['apiQueryKey'] . '" : term};
							},
							results : function(data)
							{
								return {results : data};
							}
						},
						formatResult : function(result)
						{
							if (consoleResult == true)
							{
								console.log(result);
							}

							return  result.' . $this->element['apiValueField'] . ';
						},
						formatSelection : function(result)
						{
							return result.' . $this->element['apiValueField'] . ';
						},
						dropdownCssClass: "bigdrop",
						escapeMarkup: function (m) { return m; },
					});

					$node.on("change", function(e)
					{
						window.' . $this->element['onChangeCallback'] . '(e, $node);
					});
				});

			})(jQuery);
		';

		$asset->internalJS($script);

		$attrs = array(
			'id'    => $this->id,
			'name'  => $this->name,
			'class' => $this->class
		);

		$html = new \Windwalker\Html\HtmlElement('input', '', $attrs);

		return (string) $html;
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

		$asset->addJs('library/select2/select2.js');
		$asset->addCss('library/select2/select2.css');

		self::$initialized = true;
	}
}
