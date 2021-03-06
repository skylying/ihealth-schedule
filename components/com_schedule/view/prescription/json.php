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
class ScheduleViewPrescriptionJson extends ApiView
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
	protected $name = 'prescription';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'prescription';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'prescriptions';

	/**
	 * prepareData
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
		$data = $this->getData();

		$item = $this->get('Item');

		$item->schedules = $this->get('Schedules', null, array($item->id));
		$item->drugs = $this->get('Drugs', null, array($item->id));
		$item->images = $this->get('Images', null, array($item->id));

		$data['item'] = $item;
	}
}
