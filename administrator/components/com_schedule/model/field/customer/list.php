<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('AjaxItemList');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldCustomer_List extends JFormFieldAjaxItemList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'Customer_List';

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
}
