<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;

/**
 * Class ScheduleControllerCustomersAjaxJson
 *
 * @since 1.0
 */
class ScheduleControllerCustomersAjaxJson extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$query = $this->input->getString('q');

		/** @var ScheduleModelCustomers $model */
		$model = $this->getModel('Customers', '', array('ignore_request' => true));
		$state = $model->getState();
		$limit = $state->get('list.limit', 100);

		$state->set(
			'search',
			array(
				'customer.name' => $query,
			)
		);

		$state->set('list.limit', 0);

		$items = array();
		$equalItems = array();

		foreach ($model->getItems() as $item)
		{
			$data = array(
				'id' => $item->id,
				'name' => $item->name,
				'value' => $item->id,      // For AjaxChosen
				'text' => $item->name,     // For AjaxChosen
			);

			if ($query === $item->name)
			{
				$equalItems[] = $data;
			}
			else
			{
				$items[] = $data;
			}
		}

		// Limit results
		$max = count($items) + count($equalItems);
		$max = ($limit > $max ? $limit : $max) - count($equalItems);
		$items = array_merge($equalItems, array_slice($items, 0, $max));

		if (count($items) > 0)
		{
			jexit(json_encode($items));
		}

		jexit('{}');
	}
}
