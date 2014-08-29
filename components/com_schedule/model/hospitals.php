<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Table\Table;
use Windwalker\Model\Helper\QueryHelper;
use Schedule\Helper\HospitalHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelHospitals
 *
 * @since 1.0
 */
class ScheduleModelHospitals extends \Windwalker\Model\ListModel
{
	use \Schedule\Model\Traits\ExtendedListModelTrait;

	/**
	 * configureTables
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$this->addTable('hospital', Table::HOSPITALS);
	}

	/**
	 * postGetQuery
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
		// Reset select to avoid redundant columns
		$query->clear('select')
			->select($this->getSelectFields(QueryHelper::COLS_WITH_FIRST));
	}

	/**
	 * getItems
	 *
	 * @return  mixed
	 */
	public function getItems()
	{
		$items = parent::getItems();
		$images = $this->getImages();

		foreach ($items as &$item)
		{
			$item->images = empty($images[$item->id]) ? array() : $images[$item->id];
		}

		return $items;
	}

	/**
	 * getImages
	 *
	 * @return  array
	 */
	public function getImages()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$select = ['`hospital_id`', '`title`', '`path`'];
		$query->select($select)
			->from(Table::IMAGES)
			->where('`type` = "hospital"');

		$result = array();

		foreach ($db->setQuery($query)->loadObjectList() as $image)
		{
			$image->purpose = HospitalHelper::getImageSuffix($image->path);

			if (!array_key_exists($image->hospital_id, $result))
			{
				$result[$image->hospital_id] = [];
			}

			$result[$image->hospital_id][] = $image;
		}

		return $result;
	}
}
