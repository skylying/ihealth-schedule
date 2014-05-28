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
 * Class ScheduleViewCustomerJson
 *
 * @since 1.0
 */
class ScheduleViewCustomerJson extends ApiView
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
	protected $name = 'customer';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'customer';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'customers';

	/**
	 * prepareData
	 *
	 * @throws  \Exception
	 * @return  void
	 */
	protected function prepareData()
	{
		$data = $this->getData();

		$data['item'] = $this->get('Item');

		if (empty($data['item']->id))
		{
			throw new \Exception('Item not found', 404);
		}
	}
}
