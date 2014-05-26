<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Controller\Api;

use Windwalker\Controller\State\DeleteController;

/**
 * Class ApiDeleteController
 *
 * @since 1.0
 */
class ApiDeleteController extends DeleteController
{
	/**
	 * Check session token or die.
	 *
	 * @return void
	 */
	protected function checkToken()
	{
	}

	/**
	 * Method to check delete access.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowDelete($data = array(), $key = 'id')
	{
		return true;
	}
}
