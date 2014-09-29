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

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';
JForm::addFieldPath(WINDWALKER_SOURCE . '/Form/Fields');
JFormHelper::loadFieldClass('itemlist');

/**
 * Supports an HTML select list of categories
 *
 * XML properties:
 *
 * - viewList:      (required) List name (ex: customers)
 * - ajaxTask:      (required) API task name
 * - ajaxTermKey:   (optional) API term key, default is "q"
 * - ajaxQuery:     (optional) API query parts, ex: 'foo=bar&xyz=123'
 * - ajaxMethod:    (optional) API http request method, default is "GET"
 * - tableName:     (optional) Table name, overwrite default generated table name
 * - keyField:      (optional) List key field (default is "id")
 * - valueField:    (optional) List key field (default is "title")
 * - minTermLength: (optional) Minimum term length, default is 2
 */
class JFormFieldAjaxItemList extends JFormFieldItemlist
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'AjaxItemList';

	/**
	 * Property keyField.
	 *
	 * @var string
	 */
	protected $keyField = 'id';

	/**
	 * Property valueField.
	 *
	 * @var string
	 */
	protected $valueField = 'title';

	/**
	 * Property tableName.
	 *
	 * @var  string
	 */
	protected $tableName = '';

	/**
	 * Property ajaxTask.
	 *
	 * @var string
	 */
	protected $ajaxTask;

	/**
	 * Property ajaxTermKey.
	 *
	 * @var string
	 */
	protected $ajaxTermKey = 'q';

	/**
	 * Property ajaxQuery.
	 *
	 * @var string
	 */
	protected $ajaxQuery = '';

	/**
	 * Property ajaxMethod.
	 *
	 * @var string
	 */
	protected $ajaxMethod = 'GET';

	/**
	 * Property minTermLength.
	 *
	 * @var int
	 */
	protected $minTermLength = 2;

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param SimpleXMLElement $element The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param mixed            $value   The form field value to validate.
	 * @param string           $group   The field name group control value. This acts as as an array container for the field.
	 *                                  For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                  full field name would end up being "bar[foo]".
	 *
	 * @throws InvalidArgumentException
	 * @return boolean True on success.
	 *
	 * @since 11.1
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if (false === $return)
		{
			return false;
		}

		$this->view_list = XmlHelper::get($element, 'viewList', $this->view_list);
		$this->keyField = XmlHelper::get($element, 'keyField', 'id');
		$this->element['key_field'] = $this->key_field = $this->keyField;
		$this->valueField = XmlHelper::get($element, 'valueField', 'title');
		$this->element['value_field'] = $this->value_field = $this->valueField;
		$this->ajaxTask = XmlHelper::get($element, 'ajaxTask');
		$this->ajaxTermKey = XmlHelper::get($element, 'ajaxTermKey', 'q');
		$this->ajaxQuery = XmlHelper::get($element, 'ajaxQuery', '');
		$this->ajaxMethod = strtoupper(XmlHelper::get($element, 'ajaxMethod', 'GET'));
		$this->minTermLength = XmlHelper::get($element, 'minTermLength', 2);

		if (empty($this->view_list))
		{
			throw new InvalidArgumentException('option "view_list" is empty');
		}

		if (empty($this->ajaxTask))
		{
			throw new InvalidArgumentException('option "ajaxTask" is empty');
		}

		if (empty($this->ajaxTermKey))
		{
			throw new InvalidArgumentException('option "ajaxTermKey" is empty');
		}

		$this->tableName = XmlHelper::get($element, 'tableName', '#__schedule_' . $this->view_list);

		$this->init();
		$this->setElement();

		return $return;
	}

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$typeaheadId = $this->id . '-typeahead';
		$class = empty($this->class) ? '' : ' class="' . $this->class . '"';
		$defaultItem = $this->getDefaultItem();
		$text = empty($defaultItem) ? '' : $defaultItem[$this->valueField];
		$quickAdd = $this->quickadd();
		$items = $this->getItems();

		if (!empty($defaultItem))
		{
			$items = array_merge(array($defaultItem), $items);
		}

		$bloodhoundOption = json_encode(
			array(
				'local' => $items,
				'remote' => JRoute::_('index.php?option=com_schedule&task=' . $this->ajaxTask . '&' . $this->ajaxTermKey . '=%QUERY', false),
				'limit' => 1000,
			)
		);

		$typeaheadOption = json_encode(
			array(
				'hint' => true,
				'highlight' => true,
				'minLength' => $this->minTermLength,
			)
		);

		$typeaheadDataset = json_encode(
			array(
				'name' => $this->id . '-dataset',
				'displayKey' => $this->valueField,
			)
		);

		$script = <<<SCRIPT
jQuery(function($)
{
	"use strict";

	var \$node = $('#{$typeaheadId}');
	var \$input = $('#{$this->id}');
	var option = {$typeaheadOption};
	var dataset = {$typeaheadDataset};
	var bloodhoundOption = {$bloodhoundOption};

	bloodhoundOption['datumTokenizer'] = Bloodhound.tokenizers.obj.whitespace(dataset.displayKey);
	bloodhoundOption['queryTokenizer'] = Bloodhound.tokenizers.whitespace;

	var engine = new Bloodhound(bloodhoundOption);

	engine.initialize();

	dataset['source'] = engine.ttAdapter();

	\$node.typeahead(option, dataset);
	\$node.on('typeahead:selected', function(e, item)
	{
		\$input.val(item['{$this->keyField}']);
	});
});
SCRIPT;

		/** @var \Windwalker\Helper\AssetHelper $asset */
		$asset = Container::getInstance('com_schedule')->get('helper.asset');

		$asset->internalJS($script);

		$html = <<<HTML
<div class="pull-left">
	<input type="text" id="{$typeaheadId}" value="{$text}" {$class} />
	<input type="hidden" name="{$this->name}" id="{$this->id}" value="{$this->value}" />
</div>
<div class="pull-left" style="margin-left: 0.5em;">
	{$quickAdd}
</div>
<div class="clearfix"></div>
HTML;

		return $html;
	}

	/**
	 * getOptions
	 *
	 * @return  array
	 */
	public function getItems()
	{
		$items = array();

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Filter requirements
			if ($requires = explode(',', (string) $option['requires']))
			{
				// Requires multilanguage
				if (in_array('multilanguage', $requires) && !JLanguageMultilang::isEnabled())
				{
					continue;
				}

				// Requires associations
				if (in_array('associations', $requires) && !JLanguageAssociations::isEnabled())
				{
					continue;
				}
			}

			$value = (string) $option['value'];

			$disabled = (string) $option['disabled'];
			$disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');

			$disabled = $disabled || ($this->readonly && $value != $this->value);

			if (false === $disabled)
			{
				$items[] = array(
					$this->keyField => $value,
					$this->valueField => JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)),
				);
			}
		}

		return $items;
	}


	/**
	 * getDefaultItems
	 *
	 * @return array|null
	 */
	protected function getDefaultItem()
	{
		$value = (string) $this->value;

		if (empty($value))
		{
			return null;
		}

		$container = Container::getInstance();
		/** @var JDatabaseDriver $db */
		$db = $container->get('db');
		$query = $db->getQuery(true);

		$quotedValue = $db->q($value);

		$quotedKeyField = $db->qn($this->keyField);
		$quotedValueField = $db->qn($this->valueField);

		$query->select([$quotedKeyField, $quotedValueField])
			->from($this->tableName)
			->where($quotedKeyField . '=' . $quotedValue);

		return $db->setQuery($query)->loadAssoc();
	}

	/**
	 * init
	 *
	 * @return  void
	 */
	protected function init()
	{
		static $initialized = false;

		if (true === $initialized)
		{
			return;
		}

		/** @var \Windwalker\Helper\AssetHelper $asset */
		$asset = Container::getInstance('com_schedule')->get('helper.asset');

		$asset->addJS('library/typeahead.js');
		$asset->addCSS('typeahead.css');

		$initialized = true;
	}

	/**
	 * Add an quick add button & modal
	 *
	 * @return string
	 */
	public function quickadd()
	{
		$html = parent::quickadd();

		$qid = $this->id . '_quickadd';

		$script = <<<QA
window.addEvent('domready', function()
{
	AKQuickAdd.option['{$qid}']['onAfterSubmitSuccess'] = function(data, selectId)
	{
		var option = this.option['{$qid}'];

		jQuery(selectId + '-typeahead').val(data[option.value_field]);
		select.val(data[option.key_field]);
	};
});
QA;

		JFactory::getDocument()->addScriptDeclaration($script);

		return $html;
	}
}
