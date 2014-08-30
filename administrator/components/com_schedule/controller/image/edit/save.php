<?php

use Windwalker\Controller\Edit\SaveController;
use Schedule\Uploader\ImageUploader;
use Windwalker\Model\Exception\ValidateFailException;

/**
 * Class ScheduleControllerImageEditSave
 *
 * @since 1.0
 */
class ScheduleControllerImageEditSave extends SaveController
{
	/**
	 * preSaveHook
	 *
	 * @return  void
	 *
	 * @throws Windwalker\Model\Exception\ValidateFailException
	 */
	protected function preSaveHook()
	{
		$files = $this->input->files->get('jform');
		$file = current($files);

		if (empty($file['name']))
		{
			throw new ValidateFailException(['未選擇上傳的圖片']);
		}

		if (empty($file['tmp_name']))
		{
			throw new ValidateFailException(['圖片上傳失敗']);
		}

		switch ($this->data['type'])
		{
			case 'rxindividual':
				$image = ImageUploader::uploadRxImage($file);
				$this->data['hospital_id'] = 0;
				break;

			case 'hospital':
				$file['suffix'] = $this->data['hospital_image_suffix'];
				$image = ImageUploader::uploadHospitalRxSample($file);
				$this->data['rx_id'] = 0;
				break;

			default:
				$image = false;
		}

		if (false === $image)
		{
			throw new ValidateFailException(['圖片上傳失敗']);
		}

		if (empty($this->data['title']))
		{
			$this->data['title'] = $file['name'];
		}

		$this->data['path'] = $image['path'];

		parent::preSaveHook();
	}
}
