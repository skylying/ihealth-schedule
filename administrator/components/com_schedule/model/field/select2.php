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
 * - minimumInputLength : required input words to trigger ajax search
 *  - EX : 2
 *
 * - apiUrl : ajax request url, MUST replace "&" with "&amp;"
 *  - EX : "index.php?option=com_schedule&amp;task=institutes.search.json"
 *
 * - apiQueryKey : query key name attached after ajax request url
 *  - EX : "filter_search"
 *  - Ajax request will become "index.php?option=com_schedule&task=institutes.search.json&filter_search="
 *
 * - apiConsoleResult : Auto console.log ajax response
 *  - EX : "true"
 *
 * - onChangeCallback : callback function when select2 activated elememnt changed
 *  - this function need to be declared in your own js code with excatly the same name
 *  - EX : "updateDeliveryDay"
 *
 * - apiTableNam : table name where initialValue came from
 *  - EX : institutes
 *
 * - allowNew : allow select2 field enter value which not included in dropdown list
 *  - EX : "true"
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

		// Check if select2 has initial value
		$initialValue = $this->value;

		$params = $this->getXmlField($initialValue);

		// Tell javascript we don't have initial value
		$params['hasInitialValue']   = 'false';

		if (isset($initialValue) && '' != $initialValue)
		{
			$initialItem = $this->getInitialItem($initialValue);

			$params['hasInitialValue']   = 'true';
			$params['tableName']   = $this->table_name;

			foreach ($initialItem as $key => $value)
			{
				$params[$key] = $value;
			}

			// This is select2 bug, we have to remove hint to get initial value
			unset($params['hint']);
		}

		$option = json_encode($params);

		$script = '
			(function($) {

				// Inject xml params
				var option = ' . $option . ';

				function select2Initialize($node)
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

							return  result.dropdowntext;
						},
						formatSelection : function(result)
						{
							return result.dropdowntext;
						},
						dropdownCssClass: "bigdrop",
						initSelection : function (element, callback) {

                            if (option.hasInitialValue == "true")
                            {
                            	callback({
									id: option.id,
									dropdowntext: option.dropdowntext
                            	});
                            }
                        },
						// Allow user to add new customer
						createSearchChoice: function (term)
						{
							if (option.allowNew == "true")
							{
								var text = term + (lastResults.some(function(r) { return r.text == term }) ? "" : " (新)");
									obj = {};

								obj["id"] = term;
								obj.dropdowntext = text;

								return obj;
							}
						}
					});

					if (option.hasInitialValue == "true")
					{
						$node.select2("readonly", true);

						if (option.tableName == "institutes")
						{
							// update "外送日" & "外送顏色" in edit view on document load
							$(document).ready(function(){
								var instituteData = {
									added : {
										"delivery_day" : option.delivery_day,
										"color" : option.hex,
										"hasInitialValue" : option.hasInitialValue
										}
									};

								// Call $node onchange callback
								window[option.onChangeCallback](instituteData, $node);
							})
						};
					};

					$node.on("change", function(e)
					{
						window[option.onChangeCallback](e, $node);
					});
				}

				// Export function
				window[option.fieldName] = {};
				window[option.fieldName].select2Initialize = select2Initialize;
			})(jQuery);
		';

		$jsToInitializeInstitueSelect2 = '
			(function ($) {
				$(document).ready(function() {

				var $node = $("#' . $this->id . '"),

					// Inject xml params
					option = ' . $option . ';

					window[option.fieldName].select2Initialize($node);
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
	 * Get all xml form field value
	 *
	 * @param  integer $fieldNameSpace
	 *
	 * @return array
	 */
	public function getXmlField($fieldNameSpace)
	{
		$params = array(
			'fieldName'          => $this->fieldname . $fieldNameSpace,
			'minimumInputLength' => XmlHelper::get($this->element, 'minimumInputLength', 2),
			'hint'               => XmlHelper::get($this->element, 'hint'),
			'apiUrl'             => XmlHelper::get($this->element, 'apiUrl'),
			'apiDataType'        => XmlHelper::get($this->element, 'apiDataType', 'json'),
			'consoleResult'      => XmlHelper::get($this->element, 'consoleResult', 'false'),
			'apiQueryKey'        => XmlHelper::get($this->element, 'apiQueryKey'),
			'allowNew'           => XmlHelper::get($this->element, 'allowNew', 'false'),
			'onChangeCallback'   => XmlHelper::get($this->element, 'onChangeCallback')
		);

		return $params;
	}

	/**
	 * Get select2 initial value in edit view
	 *
	 * @param integer $queryId
	 *
	 * @return mixed
	 */
	public function getInitialItem($queryId)
	{
		// Prepare database object
		$container = Container::getInstance();
		$db        = $container->get('db');
		$q         = $db->getQuery(true);

		$this->table_name  = XmlHelper::get($this->element, 'apiTableName');
		$selectString = $this->getSelectString($this->table_name);

		// Do query
		$q->select($selectString)
			->from("#__schedule_" . $this->table_name)
			->where("id = {$queryId}");

		$db->setQuery($q);

		return new JData($db->loadObject());
	}

	/**
	 * getTableField according to apiTableName input
	 *
	 * @param  string $table_name
	 *
	 * @return string
	 */
	public function getSelectString($table_name)
	{
		switch ($table_name)
		{
			case 'customers':
				$selectString = "`id`, `name` AS `dropdowntext`";

			break;

			case 'institutes':
				$selectString = "`id`, `short_title` AS `dropdowntext`, `color_hex` AS `hex`, `delivery_weekday` AS `delivery_day`, `floor` AS `floor`";

			break;

			default:
				$selectString = "`id`, `title` AS `dropdowntext`";
		}

		return $selectString;
	}
}
