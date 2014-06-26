<?php

use Windwalker\DI\Container;
use Windwalker\Helper\XmlHelper;
use Windwalker\Data\Data;
use Windwalker\Html\HtmlElement;

/**
 * Class JFormFieldSelect2
 *
 * XML Properties:
 *
 * - query:              (Required) SQL statement to get initial item data
 *                       SQL statement can use "%s" keyword to be replace by field value
 *                       EX: SELECT * FROM some_table WHERE id = %s
 * - idField:            (Required) Determine the id filed name, use this field name to get Selection's value
 * - textField:          (Required) Determine the text filed name, use this field name to get Selection's display text
 * - apiUrl:             (Optional) Ajax request url, MUST replace "&" with "&amp;" (Default: null)
 *                       EX: "index.php?option=com_schedule&amp;task=institutes.search.json"
 * - apiQueryKey:        (Optional) Query key name attached after ajax request url
 *                       If ajax request is "index.php?option=com_schedule&task=institutes.search.json&filter_search="
 *                       The apiQueryKey will be "filter_search"
 * - apiDataType:        (Optional) Ajax data-type, could use "xml", "json" and "jsonp" (Default: "json")
 * - minimumInputLength: (Optional) Number of characters necessary to start a search (Default: 2)
 * - enableComboBox:     (Optional) Enable combo-box support, (Default: false)
 * - readonly:           (Optional) Setup readonly property (Default: false)
 * - onchange:           (Optional) Setup an onchange javascript, script should be a callback function (Default: null)
 *                       Ex: function(e, $node) { console.log(e); }
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
		// Check if js/css library are included or not
		$this->init();

		/** @var \Windwalker\Helper\AssetHelper $asset */
		$asset = Container::getInstance('com_schedule')->get('helper.asset');

		$id        = $this->id;
		$onchange  = empty($this->onchange) ? 'null' : $this->onchange;
		$namespace = $this->fieldname . $this->value;
		$config    = json_encode($this->getXmlField());

		$script = <<<JAVASCRIPT
jQuery(function($)
{
	var config = {$config};

	config.onchange = {$onchange};

	Select2Helper.setConfig('{$namespace}', config);

	Select2Helper.select2('{$namespace}', $('#{$id}'));
});
JAVASCRIPT;

		$asset->internalJS($script);

		$attributes = [
			'id'    => $this->id,
			'name'  => $this->name,
			'class' => $this->class,
			'value' => $this->value,
		];

		$html = new HtmlElement('input', '', $attributes);

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
		$asset->addJs('js/select2-helper.js');
		$asset->addCss('library/select2/select2.css');

		self::$initialized = true;
	}

	/**
	 * Get all xml form field value
	 *
	 * @return array
	 */
	public function getXmlField()
	{
		$item = $this->getInitialItem();

		$params = array(
			'minimumInputLength' => XmlHelper::get($this->element, 'minimumInputLength', 2),
			'apiUrl'             => XmlHelper::get($this->element, 'apiUrl'),
			'apiDataType'        => XmlHelper::get($this->element, 'apiDataType', 'json'),
			'apiQueryKey'        => XmlHelper::get($this->element, 'apiQueryKey', 'q'),
			'idField'            => XmlHelper::get($this->element, 'idField', 'id'),
			'textField'          => XmlHelper::get($this->element, 'textField', 'text'),
			'enableComboBox'     => XmlHelper::getBool($this->element, 'enableComboBox', false),
			'initialData'        => (object) iterator_to_array($item),
			'placeholder'        => $this->hint,
			'readonly'           => $this->readonly,
		);

		return $params;
	}

	/**
	 * Get select2 initial item data
	 *
	 * @return Data
	 */
	public function getInitialItem()
	{
		$db    = Container::getInstance()->get('db');
		$query = XmlHelper::get($this->element, 'query', '');

		if (! empty($this->value) && ! empty($query))
		{
			$query = str_replace('%s', $this->value, $query);

			$item = $db->setQuery($query)->loadObject();

			if (empty($item))
			{
				return $this->getDefaultItem();
			}

			$item->_new = false;

			return new Data($item);
		}

		return $this->getDefaultItem();
	}

	/**
	 * getDefaultItem
	 *
	 * @return  Data
	 */
	protected function getDefaultItem()
	{
		$idField = XmlHelper::get($this->element, 'idField', 'id');
		$textField = XmlHelper::get($this->element, 'textField', 'text');
		$item = new stdClass;

		$item->{$idField} = $this->value;
		$item->{$textField} = $this->value . ' (新)';
		$item->_new = true;

		return new Data($item);
	}
}
