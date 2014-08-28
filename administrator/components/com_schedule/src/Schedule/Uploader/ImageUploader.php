<?php
/**
 * Part of crm project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Uploader;

use SMS\StorageFactory;
use Windwalker\Helper\ArrayHelper;

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
			if ($params->get('s3.enable', 0))
			{
				$path = self::uploadToS3($dest, $folderName);

				if (false === $path)
				{
					return false;
				}
			}

			return array(
				'file' => $file,
				'dest' => $dest,
				'path' => $path,
			);
		}

		return false;
	}

	/**
	 * uploadToS3
	 *
	 * @param string $localFilePath Local file path
	 * @param string $folderName    Upload destination folder name
	 *
	 * @return string S3 URL
	 */
	public static function uploadToS3($localFilePath, $folderName)
	{
		$s3 = self::getS3Instance();

		$remoteFilePath = $folderName . '/' . basename($localFilePath);

		$result = $s3->put($localFilePath, $remoteFilePath);

		return ArrayHelper::getValue($result, 'ObjectURL', false);
	}

	/**
	 * deleteFromS3
	 *
	 * @param string $url Remote file URL
	 *
	 * @return  void
	 */
	public static function deleteFromS3($url)
	{
		$params = \JComponentHelper::getParams('com_schedule');

		$urlPrefix = $params->get('s3.url_prefix', '');

		if (0 === strpos($url, $urlPrefix))
		{
			$path = substr($url, strlen($urlPrefix));

			self::getS3Instance()->delete($path);
		}
	}

	/**
	 * getS3Instance
	 *
	 * @return \SMS\S3
	 */
	protected static function getS3Instance()
	{
		$params = \JComponentHelper::getParams('com_schedule');
		$config = array(
			'key' => $params->get('s3.key'),
			'secret' => $params->get('s3.secret'),
			'bucket' => $params->get('s3.bucket'),
			'region' => $params->get('s3.region'),
		);

		return StorageFactory::factory('S3', $config);
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
