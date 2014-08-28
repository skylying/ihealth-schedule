<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Model\AdminModel;
use Schedule\Table\Table;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelHospital
 *
 * @since 1.0
 */
class ScheduleModelHospital extends AdminModel
{
	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'schedule';

	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option = 'com_schedule';

	/**
	 * Property textPrefix.
	 *
	 * @var string
	 */
	protected $textPrefix = 'COM_SCHEDULE';

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'hospital';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'hospital';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'hospitals';

	/**
	 * Method to set new item ordering as first or last.
	 *
	 * @param   JTable $table    Item table to save.
	 * @param   string $position 'first' or other are last.
	 *
	 * @return  void
	 */
	public function setOrderPosition($table, $position = 'last')
	{
		parent::setOrderPosition($table, $position);
	}

	/**
	 * prepareTable
	 *
	 * @param JTable $table
	 *
	 * @return  void
	 */
	public function prepareTable(JTable $table)
	{
		$tableCity = $this->getTable('City');
		$tableCity->load($table->city);
		$table->city_title = $tableCity->title;

		$tableArea = $this->getTable('Area');
		$tableArea->load($table->area);
		$table->area_title = $tableArea->title;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		if (empty($item))
		{
			return $item;
		}

		$imageIdList = $this->getImageIdList($item->id);

		$item->image1 = empty($imageIdList['reserve']) ? 0 : $imageIdList['reserve'];
		$item->image2 = empty($imageIdList['form']) ? 0 : $imageIdList['form'];

		return $item;
	}

	/**
	 * getImageList
	 *
	 * @param int $id Hospital ID
	 *
	 * @return array
	 */
	protected function getImageIdList($id)
	{
		$imageIdList = array();

		if ($id > 0)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('id, path')
				->from(Table::IMAGES)
				->where('hospital_id=' . $id);

			$images = $db->setQuery($query)->loadObjectList();

			foreach ($images as $image)
			{
				if (preg_match('/\-reserve\./i', $image->path))
				{
					$imageIdList['reserve'] = $image->id;
				}
				elseif (preg_match('/\-form\./i', $image->path))
				{
					$imageIdList['form'] = $image->id;
				}
			}

			// Fallback for files which file name without suffix
			if (empty($imageIdList))
			{
				foreach (['reserve', 'form'] as $index => $key)
				{
					if (!empty($images[$index]))
					{
						$imageIdList[$key] = $images[$index]->id;
					}
				}
			}
		}

		return $imageIdList;
	}
}
