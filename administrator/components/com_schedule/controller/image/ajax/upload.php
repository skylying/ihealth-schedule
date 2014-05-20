<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Table\Table;
use Schedule\Helper\ImageHelper;

/**
 * Class ScheduleControllerImageAjaxUpload
 *
 * @since 1.0
 */
class ScheduleControllerImageAjaxUpload extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$rxId        = $this->input->getInt("rxId", 0);
		$files       = $this->input->files->getVar('image');
		$imageMapper = new DataMapper(Table::IMAGES);

		ImageHelper::handleUpload($rxId, array($files));

		echo json_encode($imageMapper->findOne(array(), array("id DESC")));

		jexit();
	}
}

