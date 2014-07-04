<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Table\Table;
use Windwalker\Model\Helper\QueryHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelHospitals
 *
 * @since 1.0
 */
class ScheduleModelHospitals extends \Windwalker\Model\ListModel
{
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
		$queryHelper = $this->container->get('model.' . $this->getName() . '.helper.query');

		// Reset select to avoid redundant columns
		$query->clear('select')
			->select($queryHelper->getSelectFields(QueryHelper::COLS_WITH_FIRST));
	}

	/**
	 * getItems
	 *
	 * @return  mixed
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $item)
		{
			$this->injectImageInfo($item);
		}

		return $items;
	}

	/**
	 * injectImageInfo
	 *
	 * @param  {object} $item
	 *
	 * @return void
	 */
	public function injectImageInfo($item)
	{
		$item->images = array();

		$imagesInfo = $this->getImages();

		foreach ($imagesInfo as $image)
		{
			$tmp = [];

			if ($image->hospital_id == $item->id)
			{
				$tmp['title'] = $image->title;
				$tmp['path'] = $image->path;
				$tmp['purpose'] = (strpos($image->title, 'reserve') !== false) ? 'reserve' : 'form';

				array_push($item->images, $tmp);
			}
		}
	}

	/**
	 * getImages
	 *
	 * @return  mixed
	 */
	public function getImages()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$select = ['`hospital_id`', '`title`', '`path`'];
		$query->select($select)
			->from(Table::IMAGES)
			->where('`type` = "hospital"');

		$result = $db->setQuery($query)->loadObjectList();

		return $result;
	}
}
