<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\DisplayController;
use Schedule\Uploader\ImageUploader;

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
	 * @return void
	 *
	 * @throws InvalidArgumentException
	 */
	protected function doExecute()
	{
		$file = $this->input->files->get('image');

		if (empty($file))
		{
			$this->error('upload file is empty');
		}

		$model = $this->getModel('Image');
		$type = $this->input->getString('type');
		$file['suffix'] = $this->input->getString('suffix');

		if (!in_array($type, ['rxindividual', 'hospital']))
		{
			$this->error(sprintf('Invalid image type "%s"', $type));
		}

		switch ($type)
		{
			case 'rxindividual':
				$image = ImageUploader::uploadRxImage($file);
				break;

			case 'hospital':
			default:
				$image = ImageUploader::uploadHospitalRxSample($file);
		}

		if (false === $image)
		{
			$this->error('Upload image failed');
		}

		$data = $this->input->get('jform', array(), 'ARRAY');

		$data['success'] = true;
		$data['type'] = $type;
		$data['title'] = $image['file']['name'];
		$data['path'] = $image['path'];

		$model->save($data);

		$data['id'] = $model->getState()->get('image.id');
		$data['url'] = JUri::root(true) . $data['path'];

		// Generate thumbnail
		$data['thumb'] = array(
			'url' => $data['url'],
			'width' => 360,
			'height' => 360,
		);

		// Save to S3

		jexit(json_encode($data));
	}

	/**
	 * error
	 *
	 * @param string $message
	 *
	 * @return  void
	 */
	protected function error($message)
	{
		$data = array(
			'success' => false,
			'error' => $message,
		);

		jexit(json_encode($data));
	}
}
