<?php
/**
 * Part of crm project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

use Windwalker\Data\NullData;
use Windwalker\Helper\StringHelper;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Windwalker\Data\DataSet;
use Schedule\Table\Table;

/**
 * Class ImageHelper
 *
 * @since 1.0
 */
class ImageHelper
{
	/**
	 * Property extMapper.
	 *
	 * @var  array
	 */
	static protected $extMapper = array(
		'image/jpeg' => 'jpg',
		'image/png'  => 'png'
	);

	/**
	 * getImages
	 *
	 * @param int    $foreignId
	 * @param string $imageType
	 *
	 * @return  array|mixed
	 */
	public static function getImages($foreignId, $imageType)
	{
		if (!$foreignId)
		{
			return array();
		}

		$searchArray = ($imageType == 'rxindividual') ? ['rx_id' => $foreignId] : ['hospital_id' => $foreignId];

		return (new DataMapper(Table::IMAGES))->find($searchArray);
	}

	/**
	 * Do the file upload and update database as well
	 *
	 * @param int    $foreignId
	 * @param string $imageType
	 * @param array  $files
	 * @param string $purpose
	 *
	 * @return  array|mixed
	 */
	public static function handleUpload($foreignId, $imageType, $files, $purpose)
	{
		$images = array();

		foreach ($files as $file)
		{
			if (empty($file['tmp_name']))
			{
				continue;
			}

			$imageName = md5_file($file['tmp_name']);

			$ext = \JArrayHelper::getValue(static::$extMapper, $file['type']);

			if (!is_dir(JPATH_ROOT . '/' . static::getStoragePath($foreignId, $imageType)))
			{
				\JFolder::create(JPATH_ROOT . '/' . static::getStoragePath($foreignId, $imageType));
			}

			$imagePath  = static::getStoragePath($foreignId, $imageType) . '/' . $imageName . '.' . $ext;

			\JFile::upload($file['tmp_name'], JPATH_ROOT . '/' . $imagePath);

			$images[] = array(
				"name" => $file['name'],
				"path"  => $imagePath
			);
		}

		static::saveImages($foreignId, $imageType, $images, $purpose);

		return static::getImages($foreignId, $imageType);
	}

	/**
	 * getStoragePath
	 *
	 * @param int    $foreignId
	 * @param string $imageType
	 *
	 * @return  string
	 */
	public static function getStoragePath($foreignId, $imageType)
	{
		// TODO: 設定到 xml 的設定檔
		return 'media/com_schedule/upload/' . $imageType . '/' . $foreignId;
	}

	/**
	 * saveImages
	 *
	 * @param int    $foreignId
	 * @param string $imageType
	 * @param array  $images
	 * @param string $purpose
	 *
	 * @return  array
	 */
	public static function saveImages($foreignId, $imageType, $images, $purpose)
	{
		$imageMapper = new DataMapper(Table::IMAGES);

		$data = array();

		foreach ($images as $image)
		{
			$columnList = array(
				"title" => $image["name"],
				"path" => $image["path"],
				"type" => $imageType,
			);

			// Title 後綴圖片用途, 避免官網撈錯圖片
			if (!empty($purpose))
			{
				$columnList['title'] = $image["name"] . '-' . $purpose;
			}

			$imageType == 'rxindividual' ? $columnList['rx_id'] = $foreignId : $columnList['hospital_id'] = $foreignId;

			$data[] = new Data($columnList);
		}

		$dataSet = new DataSet($data);

		$imageMapper->create($dataSet);

		return $data;
	}

	/**
	 * removeImages
	 *
	 * @param array $cid
	 *
	 * @return  bool
	 */
	public static function removeImages($cid = array())
	{
		$cid = (array) $cid;

		if (empty($cid))
		{
			return true;
		}

		$imageMapper = new DataMapper(Table::IMAGES);

		$images = $imageMapper->find(array("id" => $cid));

		foreach ($images as $image)
		{
			// Remove file
			\JFile::delete(JPATH_ROOT . '/' . $image->path);
		}

		// Sql remove
		$imageMapper->delete(array("id" => $cid));

		return true;
	}

	/**
	 * resetImagesRxId
	 *
	 * @param array  $cid
	 * @param int    $foreignId
	 * @param string $imageType
	 *
	 * @return  void
	 */
	public static function resetImagesRxId(array $cid, $foreignId, $imageType)
	{
		$imageMapper = new DataMapper(Table::IMAGES);

		$images = $imageMapper->find(array("id" => $cid));

		foreach ($images as $image)
		{
			$newJoomlaDir      = static::getStoragePath($foreignId, $imageType);
			$newSystemPath     = JPATH_ROOT . '/' . $newJoomlaDir;
			$oldSystemFilePath = JPATH_ROOT . '/' . $image->path;
			$fileName          = basename($oldSystemFilePath);

			// If dir not set yet
			if (! is_dir($newSystemPath))
			{
				\JFolder::create($newSystemPath);
			}

			// File move
			\JFile::move($oldSystemFilePath, "{$newSystemPath}/{$fileName}");

			// Flush database
			($imageType == 'rxindividual') ? $image->rx_id = $foreignId : $image->hospital_id = $foreignId;
			$image->path  = "{$newJoomlaDir}/{$fileName}";

			// Save data
			$imageMapper->updateOne($image);
		}
	}
}
