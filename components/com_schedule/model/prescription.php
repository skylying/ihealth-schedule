<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleModelPrescription
 *
 * @since 1.0
 */
class ScheduleModelPrescription extends \Windwalker\Model\AdminModel
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
	protected $name = 'prescription';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'prescription';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'prescriptions';
}
