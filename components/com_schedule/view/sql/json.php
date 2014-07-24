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
 * Class ScheduleViewPrescriptionsJson
 *
 * @since 1.0
 */
class ScheduleViewSqlJson extends ApiView
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
	protected $name = 'sql';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'sql';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'sqls';

	/**
	 * prepareData
	 *
	 * @throws  Exception
	 * @return  void
	 */
	protected function prepareData()
	{
		if (!JDEBUG)
		{
			throw new \Exception('Not support in production mode.');
		}

		$data = $this->getData();

		$data['result'] = $this->get('Item');
	}
}
