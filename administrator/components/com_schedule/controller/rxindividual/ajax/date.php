<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;
use \Schedule\Table\Table;
use Windwalker\Joomla\DataMapper\DataMapper;

/**
 * Class ScheduleControllerRxindividualAjaxDate
 *
 * @since 1.0
 */
class ScheduleControllerRxindividualAjaxDate extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$address_id = $this->input->get('address_id');
		$date = new \DateTime($this->input->get('see_dr_date'));

		$addressMapper = new DataMapper(Table::ADDRESSES);
		$routeMapper   = new DataMapper(Table::ROUTES);

		$address = $addressMapper->findOne($address_id);

		$route   = $routeMapper->findOne(array("city" => $address->city, "area" => $address->area));

		show($route);
		echo $route->weekday;

		jexit();
	}
}

