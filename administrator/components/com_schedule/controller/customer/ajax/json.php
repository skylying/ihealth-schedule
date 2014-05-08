<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;

/**
 * Class ScheduleControllerCustomerAjaxJson
 *
 * @since 1.0
 */
class ScheduleControllerCustomerAjaxJson extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		\JFactory::getDocument()->setMimeEncoding('application/json');

		$id = $this->input->get('id');
		$queryString = $this->input->getString('filter_search');

		// 盧立偉要用的部分, 暫時用 if 包起來, 裡面沒動到
		if (!empty($id))
		{
			$model = $this->getModel('Customer');

			$data = $model->getItem($id);
		}

		if (!empty($queryString))
		{
			// Get institute_id
			$instituteId = $this->input->get('institute_id');

			$model = $this->getModel('Customers', '', array('ignore_request' => true));

			$state = $model->getState();

			// Set default state back to model
			$state->set('list', (object) array
				(
					'direction'    => 'ASC',
					'ordering'     => 'customer.id',
					'fullordering' => '',
					'limit'        => 100,
					'start'	       => 0
				)
			);

			// Set institute filter from ajax request
			if (isset($instituteId) && '' != $instituteId)
			{
				$state->set('filter', array('customer.institute_id' => $instituteId));
			}

			// Set search keyword from ajax request
			$state->set('search', array('customer.name' => $queryString));

			$data = $model->getItems();
		}

		echo json_encode($data);

		jexit();
	}
}
