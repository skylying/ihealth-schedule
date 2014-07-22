<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

use Schedule\Table\Table;
use Windwalker\Joomla\DataMapper\DataMapper;

/**
 * Class AreaHelper
 *
 * @since 1.0
 */
class AreaHelper
{
	/**
	 * getAreaTitle
	 *
	 * @param   int $id
	 *
	 * @return  mixed
	 */
	public static function getAreaTitle($id)
	{
		return (new DataMapper(Table::AREAS))->findOne(['id' => $id])->title;
	}
}
