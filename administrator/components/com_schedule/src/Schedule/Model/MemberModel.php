<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Schedule\Model;

use Windwalker\Model\AdminModel;

// No direct access
defined('_JEXEC') or die;

/**
 * Class Schedule\Model\Member
 *
 * @since 1.0
 */
class MemberModel extends AdminModel
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
	 * @var  string
	 */
	protected $textPrefix = 'COM_SCHEDULE';

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'member';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'member';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'members';

	/**
	 * Method to set new item ordering as first or last.
	 *
	 * @param   \JTable  $table     Item table to save.
	 * @param   string   $position  'first' or other are last.
	 *
	 * @return  void
	 */
	public function setOrderPosition($table, $position = 'last')
	{
		parent::setOrderPosition($table, $position);
	}
}
