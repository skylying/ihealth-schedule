<?php

use \Windwalker\DI\Container;
use \Windwalker\Helper\XmlHelper;

/**
 * Class JFormFieldSelect2
 *
 * XML Property list :
 *
 * hint               @ placeholder
 * EX : "輸入機構名稱"
 *
 * minimumInputLength @ required input words to trigger ajax search
 * EX : 2
 *
 * apiUrl             @ ajax request url, MUST replace "&" with "&amp;"
 * EX : "index.php?option=com_schedule&amp;task=institutes.search.json"
 *
 * apiQueryKey        @ query key name attached after ajax request url
 * EX : "filter_search"
 * Ajax request will become "index.php?option=com_schedule&task=institutes.search.json&filter_search="
 *
 * apiValueField      @ property name you want to show in dropdown list
 * EX : if you have json response like { "id" : "1", "name" : "" }
 *      you should put "name" in this field
 *
 * apiConsoleResult   @ Auto console.log ajax response
 * EX : "true"
 *
 * onChangeCallback   @ callback function when select2 activated elememnt changed
 *                      this function need to be declared in your own js code with excatly the same name
 * EX : "updateDeliveryDay"
 *
 * allowNew           @ allow select2 field enter value which not included in dropdown list
 * EX : "true"
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

		$option = self::getXmlField();

		$script = '
			(function($) {

				// Inject xml params
				var option = ' . $option . ';

				function initialize($node)
				{
					var lastResults = [];

					$node.select2({
						minimumInputLength : option.minimumInputLength,
						placeholder : option.hint,
						ajax : {
							url : option.apiUrl,
							dataType : option.apiDataType,
							data: function(term)
							{
								var obj = {};

									obj[option.apiQueryKey] = term;
									obj["institute_id"] = window.instituteId;

								return obj;
							},
							results : function(data)
							{
								lastResults = data;
								return {results : data};
							}
						},
						formatResult : function(result)
						{
							if (option.consoleResult == "true")
							{
								console.log(result);
							}

							return  result[option.apiValueField];
						},
						formatSelection : function(result)
						{
							return result[option.apiValueField];
						},
						dropdownCssClass: "bigdrop",

						// Allow user to add new customer
						createSearchChoice: function (term)
						{
							if (option.allowNew == "true")
							{
								var text = term + (lastResults.some(function(r) { return r.text == term }) ? "" : " (new)");
									obj = {};

								obj["id"] = term;
								obj[option.apiValueField] = text;

								return obj;
							}
						}
					});

					$node.on("change", function(e)
					{
						window[option.onChangeCallback](e, $node);
					});
				}

				// Export function
				window[option.fieldName] = {};
				window[option.fieldName].initialize = initialize;
			})(jQuery);
		';

		$jsToInitializeInstitueSelect2 = '
			(function ($) {
				$(document).ready(function() {

				var $node = $("#' . $this->id . '"),

					// Inject xml params
					option = ' . $option . ';

					window[option.fieldName].initialize($node);
				});
			})(jQuery);
		';

		$asset->internalJS($script);
		$asset->internalJS($jsToInitializeInstitueSelect2);

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

	/**
	 * Get all xml field value
	 *
	 * @return string (json format)
	 */
	public function getXmlField()
	{
		$params = array(
			'fieldName'          => $this->fieldname,
			'minimumInputLength' => XmlHelper::get($this->element, 'minimumInputLength', 2),
			'hint'               => XmlHelper::get($this->element, 'hint'),
			'apiUrl'             => XmlHelper::get($this->element, 'apiUrl'),
			'apiDataType'        => XmlHelper::get($this->element, 'apiDataType', 'json'),
			'consoleResult'      => XmlHelper::get($this->element, 'consoleResult', 'false'),
			'apiQueryKey'        => XmlHelper::get($this->element, 'apiQueryKey'),
			'apiValueField'      => XmlHelper::get($this->element, 'apiValueField'),
			'allowNew'           => XmlHelper::get($this->element, 'allowNew', 'false'),
			'onChangeCallback'   => XmlHelper::get($this->element, 'onChangeCallback')
		);

		$option = json_encode($params);

		return $option;
	}
}
