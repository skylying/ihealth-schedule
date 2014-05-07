<?php
/**
 * Part of crm project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Data\NullData;
use Windwalker\Helper\StringHelper;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Windwalker\Data\DataSet;
use Schedule\Table\Table;

/**
 * Class ScheduleHelperImage
 *
 * @since 1.0
 */
class ScheduleHelperImage
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
	 * Get Images
	 *
	 * @param integer $rxId
	 *
	 * @return  array
	 */
	public static function getImages($rxId)
	{
		if (!$rxId)
		{
			return array();
		}

		return (new DataMapper(Table::IMAGES))->find(array("rx_id" => $rxId));
	}

	/**
	 * handleUpload
	 *
	 * @param integer $rxId
	 * @param array   $files
	 *
	 * @return  array
	 */
	public static function handleUpload($rxId, $files)
	{
		$images = array();

		foreach ($files as $file)
		{
			if (empty($file['tmp_name']))
			{
				continue;
			}

			$imageName = md5_file($file['tmp_name']);

			$ext = JArrayHelper::getValue(static::$extMapper, $file['type']);

			if (!is_dir(JPATH_ROOT . '/' . static::getStoragePath($rxId)))
			{
				JFolder::create(JPATH_ROOT . '/' . static::getStoragePath($rxId));
			}

			$imagePath  = static::getStoragePath($rxId) . '/' . $imageName . '.' . $ext;

			JFile::upload($file['tmp_name'], JPATH_ROOT . '/' . $imagePath);

			$images[] = array(
				"name" => $file['name'],
				"path"  => $imagePath
			);
		}

		static::saveImages($rxId, $images);

		return static::getImages($rxId);
	}

	/**
	 * getStoragePath
	 *
	 * @param integer $rxId
	 *
	 * @return  string
	 */
	public static function getStoragePath($rxId)
	{
		// TODO: 設定到 xml 的設定檔
		return 'images/upload/' . $rxId;
	}

	/**
	 * saveImages
	 *
	 * @param integer $rxId
	 * @param array   $images
	 *
	 * @return  Data
	 */
	public static function saveImages($rxId, $images)
	{
		$imageMapper = new DataMapper(Table::IMAGES);

		$data = array();

		foreach ($images as $image)
		{
			$data[] = new Data(array("title" => $image["name"], "path" => $image["path"], "rx_id" => $rxId));
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
	 * @return  void
	 */
	public static function removeImages($cid = array())
	{
		// TODO: 完成刪除圖片功能
	}
}
