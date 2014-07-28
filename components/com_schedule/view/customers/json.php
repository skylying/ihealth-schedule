<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\View\ApiView;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleViewMemberJson
 *
 * @since 1.0
 */
class ScheduleViewCustomersJson extends ApiView
{
	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'schedule';

	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option = 'com_schedule';

	/**
	 * Property textPrefix.
	 *
	 * @var string
	 */
	protected $textPrefix = 'COM_SCHEDULE';

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'customers';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'cutomer';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'customers';

	/**
	 * prepareData
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
		$data = $this->getData();

		$items = $this->get('Items');

		$data['items'] = $items;
	}
}
