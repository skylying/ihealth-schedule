<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Schedule\Table\Table;
use Schedule\Table\Collection as TableCollection;

/**
 * Class ScheduleModelPrescription
 *
 * @since 1.0
 */
class ScheduleModelSql extends \Windwalker\Model\Model
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
	protected $name = 'sql';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'sql';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'sqls';

	/**
	 * getItem
	 *
	 * @param int $pk
	 *
	 * @throws Exception
	 * @return  mixed|void
	 */
	public function getItem($pk = null)
	{
		if (!JDEBUG)
		{
			throw new \Exception('Not support in production mode.');
		}

		$input = JFactory::getApplication()->input;

		$sql    = $input->getString('sql');
		$method = $input->get('method', 'loadObjectList');
		$args   = $input->get('args', array(), 'array');

		if (!trim($sql))
		{
			throw new \RuntimeException('No Query?');
		}

		$this->db->setQuery($sql);

		return call_user_func_array(array($this->db, $method), $args);
	}
}
