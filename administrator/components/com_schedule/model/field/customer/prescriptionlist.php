<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\DI\Container;

// No direct access
defined('_JEXEC') or die;

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';
JForm::addFieldPath(WINDWALKER_SOURCE . '/Form/Fields');
JFormHelper::loadFieldClass('itemlist');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldCustomer_PrescriptionList extends JFormFieldItemlist
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'Customer_prescriptionList';

	/**
	 * List name.
	 *
	 * @var string
	 */
	protected $view_list = 'customers';

	/**
	 * Item name.
	 *
	 * @var string
	 */
	protected $view_item = 'customer';

	/**
	 * Extension name, eg: com_content.
	 *
	 * @var string
	 */
	protected $extension = 'com_schedule';

	/**
	 * Set the published column name in table.
	 *
	 * @var string
	 */
	protected $published_field = 'state';

	/**
	 * Set the ordering column name in table.
	 *
	 * @var string
	 */
	protected $ordering_field = null;

	/**
	 * Use Query to get Items.
	 *
	 * @return \stdClass[]
	 */
	public function getItems()
	{
		$published   = (string) $this->element['published'];
		$nested      = (string) $this->element['nested'];
		$key_field   = $this->element['key_field'] ? (string) $this->element['key_field'] : 'id';
		$value_field = $this->element['value_field'] ? (string) $this->element['value_field'] : 'title';
		$ordering    = $this->element['ordering'] ? (string) $this->element['ordering'] : null;
		$customer = $this->element['customer'] ? (string) $this->element['customer'] : null;
		$table_name  = $this->element['table'] ? (string) $this->element['table'] : '#__' . $this->component . '_' . $this->view_list;
		$select      = $this->element['select'];

		$container = Container::getInstance();
		$db    = $container->get('db');
		$q     = $db->getQuery(true);
		$input = $container->get('input');

		// Avoid self
		// ========================================================================
		$id     = $input->get('id');
		$option = $input->get('option');
		$view   = $input->get('view');
		$layout = $input->get('layout');

		if ($nested && $id)
		{
			$table = JTable::getInstance(ucfirst($this->view_item), ucfirst($this->component) . 'Table');
			$table->load($id);
			$q->where("id != {$id}");
			$q->where("lft < {$table->lft} OR rgt > {$table->rgt}");
		}

		if ($nested)
		{
			$q->where("( id != 1 AND `{$value_field}` != 'ROOT' )");
		}

		// Some filter
		// ========================================================================
		if ($published)
		{
			$q->where("{$this->published_field} >= 1");
		}

		// 客戶 type
		if (! empty($customer))
		{
			$q->where("`type` = '{$customer}'");
		}

		// Ordering
		$order    = $nested ? 'lft' : 'id';
		$order    = $this->ordering_field ? $this->ordering_field : $order;
		$ordering = $ordering ? $ordering : $order;

		if ($ordering != 'false')
		{
			$q->order($ordering);
		}

		// Query
		// ========================================================================
		$select = $select ? '*, ' . $select : '*';

		$q->select($select)
			->from($table_name);

		$db->setQuery($q);
		$items = $db->loadObjectList();

		$items = $items ? $items : array();

		return $items;
	}
}
