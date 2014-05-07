<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\DI\Container;
use Schedule\Table\Table;
use Windwalker\Joomla\DataMapper\DataMapper;

// No direct access
defined('_JEXEC') or die;

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';
JForm::addFieldPath(WINDWALKER_SOURCE . '/Form/Fields');
JFormHelper::loadFieldClass('itemlist');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldCustomer_Rxlist extends JFormFieldItemlist
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'Customer_Rxlist';

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
		$customer = $this->element['customer'] ? (string) $this->element['customer'] : null;
		$table_name  = $this->element['table'] ? (string) $this->element['table'] : '#__' . $this->component . '_' . $this->view_list;

		return (new DataMapper($table_name))->find(['type' => $customer]);
	}
}
