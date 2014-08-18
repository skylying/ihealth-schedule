<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\User\Member;

use JConsole\Command\JCommand;

defined('JCONSOLE') or die;

/**
 * Class Member
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Member extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	/**
	 * Console(Argument) name.
	 *
	 * @var  string
	 */
	protected $name = 'member-pass-convert';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Member operator';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'member-pass-convert <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		// $this->addCommand();

		parent::configure();
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		include_once JPATH_LIBRARIES . '/windwalker/src/init.php';

		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('*')
			->from('#__schedule_members');

		foreach ($db->setQuery($query)->loadObjectList() as $member)
		{
			if (strpos($member->password, '$2y$') === 0)
			{
				continue;
			}

			$member->password = \JUserHelper::hashPassword($member->password);

			$db->updateObject('#__schedule_members', $member, 'id');

			$this->out('Updated ID :' . $member->id . ' Name: ' . $member->name);
		}

		return true;
	}
}
