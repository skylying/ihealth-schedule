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

JFormHelper::loadFieldClass('list');

/**
 * Class JFormFieldAjaxChosen
 *
 * XML properties:
 * - viewList:      (required) List name (ex: customers)
 * - ajaxTask:      (required) API task name
 * - keyField:      (optional) List key field (default is "id")
 * - valueField:    (optional) List key field (default is "title")
 * - tableName:     (optional) Table name, overwrite default generated table name
 * - ajaxTermKey:   (optional) API term key, default is "q"
 * - ajaxQuery:     (optional) API query parts, ex: 'foo=bar&xyz=123'
 * - ajaxMethod:    (optional) API http request method, default is "GET"
 * - minTermLength: (optional) Minimum term length, default is 2
 */
class JFormFieldAjaxChosen extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	public $type = 'AjaxChosen';

	/**
	 * Property viewList.
	 *
	 * @var string
	 */
	protected $viewList;

	/**
	 * Property tableName.
	 *
	 * @var string
	 */
	protected $tableName;

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
	 * @param   SimpleXMLElement $element   The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed            $value     The form field value to validate.
	 * @param   string           $group     The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @throws  InvalidArgumentException
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if (false === $return)
		{
			return false;
		}

		$this->viewList      = XmlHelper::get($element, 'viewList');
		$this->keyField      = XmlHelper::get($element, 'keyField', 'id');
		$this->valueField    = XmlHelper::get($element, 'valueField', 'title');
		$this->ajaxTask      = XmlHelper::get($element, 'ajaxTask');
		$this->ajaxTermKey   = XmlHelper::get($element, 'ajaxTermKey', 'q');
		$this->ajaxQuery     = XmlHelper::get($element, 'ajaxQuery', '');
		$this->ajaxMethod    = strtoupper(XmlHelper::get($element, 'ajaxMethod', 'GET'));
		$this->minTermLength = XmlHelper::get($element, 'minTermLength', 2);

		if (empty($this->viewList))
		{
			throw new InvalidArgumentException('option "viewList" is empty');
		}

		if (empty($this->ajaxTask))
		{
			throw new InvalidArgumentException('option "ajaxTask" is empty');
		}

		if (empty($this->ajaxTermKey))
		{
			throw new InvalidArgumentException('option "ajaxTermKey" is empty');
		}

		$this->tableName = XmlHelper::get($element, 'tableName', '#__schedule_' . $this->viewList);

		return $return;
	}

	/**
	 * Method to get the field input for a tag field.
	 *
	 * @return  string  The field input.
	 *
	 * @since   3.1
	 */
	protected function getInput()
	{
		$ajaxQuery = (empty($this->ajaxQuery) ? '' : '&' . $this->ajaxQuery);

		$ajaxChosenOptions = new JRegistry(
			array(
				'selector'      => '#' . $this->id,
				'type'          => $this->ajaxMethod,
				'url'           => JRoute::_('index.php?option=com_schedule&task=' . $this->ajaxTask . $ajaxQuery, false),
				'dataType'      => 'json',
				'jsonTermKey'   => $this->ajaxTermKey,
				'minTermLength' => $this->minTermLength,
			)
		);

		JHtmlFormbehavior::ajaxchosen($ajaxChosenOptions);

		return parent::getInput();
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$keyField = $this->keyField;
		$valueField = $this->valueField;
		$options = array();

		foreach ($this->getDefaultItems() as $item)
		{
			$options[] = JHtmlSelect::option($item->$keyField, $item->$valueField);
		}

		$options = array_merge($options, parent::getOptions());

		return $options;
	}

	/**
	 * getDefaultItems
	 *
	 * @return  array
	 */
	protected function getDefaultItems()
	{
		$container = Container::getInstance();
		$db = $container->get('db');
		$query = $db->getQuery(true);
		$value = (array) $this->value;
		$quotedKeyField = $db->qn($this->keyField);
		$quotedValueField = $db->qn($this->valueField);
		$quotedValue = $db->q($value);

		$query->select([$quotedKeyField, $quotedValueField])
			->from($this->tableName);

		if (1 == count($value))
		{
			$query->where($quotedKeyField . '=' . $quotedValue[0]);
		}
		elseif (count($value) > 1)
		{
			$query->where($quotedKeyField . ' ' . ((string) new JDatabaseQueryElement('IN()', $quotedValue)));
		}
		else
		{
			return array();
		}

		return $db->setQuery($query)->loadObjectList();
	}
}
