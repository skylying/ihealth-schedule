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
class ScheduleViewAuthenticateJson extends ApiView
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
	protected $name = 'authenticate';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'authenticate';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'authenticates';

	/**
	 * prepareData
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
		$data = $this->getData();

		$model = $this->getModel();
	}
}
