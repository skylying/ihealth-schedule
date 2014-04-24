<?php
/**
 * Part of iHealth-schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class ScheduleControllerAddressesStateDelete
 *
 * This class if for bug that in delegator.php, $viewItem will become addres/s
 * So we override this controller with correct name
 *
 * Todo: in AbstractListController plz add $config as part of control variable
 *
 * @since 1.0
 */
class ScheduleControllerAddressesStateDelete extends \Windwalker\Controller\State\DeleteController
{
	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'address';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'addresses';

}
 