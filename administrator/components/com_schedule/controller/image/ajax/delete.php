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
use Schedule\Uploader\ImageUploader;

/**
 * Class ScheduleControllerImageAjaxDelete
 *
 * @since 1.0
 */
class ScheduleControllerImageAjaxDelete extends DisplayController
{
	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$id = $this->input->getInt('id');
		$imageMapper = new DataMapper(Table::IMAGES);

		if (!empty($id))
		{
			$image = $imageMapper->findOne($id);

			if (!$image->isNull() && $imageMapper->delete($id))
			{
				if (!preg_match('#^(http|https|ftp)://#s', $image['path']))
				{
					$dest = JPATH_ROOT . $image['path'];

					if (file_exists($dest))
					{
						unlink($dest);
					}
				}
				else
				{
					// Delete S3 file
					$params = \JComponentHelper::getParams('com_schedule');

					if ($params->get('s3.enable', 0))
					{
						ImageUploader::deleteFromS3($image['path']);
					}
				}

				jexit('{"success": true}');
			}
		}

		jexit('{"success": false}');
	}
}
