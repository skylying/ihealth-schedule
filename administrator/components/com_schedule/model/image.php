<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Model\AdminModel;
use Schedule\Helper\HospitalHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelImage
 *
 * @since 1.0
 */
class ScheduleModelImage extends AdminModel
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
	protected $name = 'image';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'image';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'images';

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
	 * getItem
	 *
	 * @param null $pk
	 *
	 * @return  mixed
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		if (empty($item))
		{
			return $item;
		}

		$item->rx_image = $item->id;
		$item->hospital_rx_sample = $item->id;
		$item->hospital_image_suffix = '-' . HospitalHelper::getImageSuffix($item->path);

		return $item;
	}
}
