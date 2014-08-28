<?php
/**
 * Part of crm project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Uploader;

/**
 * Class ImageUploader
 */
class ImageUploader
{
	/**
	 * uploadRxImage
	 *
	 * @param array $file Uploaded file information
	 *
	 * @see JInputFiles::get
	 *
	 * @return bool|array
	 */
	public static function uploadRxImage(array $file)
	{
		$folderName = \JComponentHelper::getParams('com_schedule')->get('upload.rx_image_folder', 'rx_images');

		return self::upload($file, $folderName);
	}

	/**
	 * uploadHospitalRxSample
	 *
	 * @param array $file Uploaded file information
	 *
	 * @see JInputFiles::get
	 *
	 * @return bool|array
	 */
	public static function uploadHospitalRxSample(array $file)
	{
		$folderName = \JComponentHelper::getParams('com_schedule')->get('upload.rx_image_folder', 'hospital_rx_samples');

		return self::upload($file, $folderName);
	}

	/**
	 * upload
	 *
	 * @param array  $file       Uploaded file information
	 * @param string $folderName Upload destination folder name
	 *
	 * @return bool
	 */
	public static function upload(array $file, $folderName)
	{
		if (false === self::checkUploadFile($file))
		{
			return false;
		}

		$params = \JComponentHelper::getParams('com_schedule');
		$baseDir = $params->get('upload.base_dir', '/media/com_schedule/upload');
		$hash = md5_file($file['tmp_name']);
		$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
		$suffix = ArrayHelper::getValue($file, 'suffix', '');

		$baseDir = '/' . trim($baseDir, '/ ') . '/';
		$folderName = trim($folderName, '/ ');

		$path = $baseDir . $folderName . '/' . $hash . $suffix . '.' . $ext;
		$dest = JPATH_ROOT . $path;

		if (true === \JFile::upload($file['tmp_name'], $dest))
		{
			return array(
				'file' => $file,
				'dest' => $dest,
				'path' => $path,
			);
		}

		return false;
	}

	/**
	 * checkUploadFile
	 *
	 * @param array $file Uploaded file information
	 *
	 * @return bool
	 */
	public static function checkUploadFile(array $file)
	{
		if (empty($file))
		{
			return false;
		}

		// Check input file data format
		foreach (array('name', 'type', 'tmp_name', 'error', 'size') as $key)
		{
			if (!array_key_exists($key, $file))
			{
				return false;
			}
		}

		return true;
	}
}
