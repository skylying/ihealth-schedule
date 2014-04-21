<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\User\Create;

use JConsole\Command\JCommand;
use JConsole\Prompter\NotNullPrompter;
use Joomla\Console\Prompter\PasswordPrompter;

defined('JCONSOLE') or die;

/**
 * Class Install
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Create extends JCommand
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
	protected $name = 'create';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Create User profile';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'create <option>[option]</option>';

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
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @return int
	 */
	protected function doExecute()
	{
		if (!version_compare(PHP_VERSION, '5.4', '>='))
		{
			throw new \RuntimeException('PHP need 5.4 or upper.');
		}

		// Install User
		$userdata = array();

		$userdata['username'] = (new NotNullPrompter)->ask('Please enter account: ');

		$userdata['name'] = (new NotNullPrompter)->ask('Please enter user name: ');

		$userdata['email'] = (new NotNullPrompter)->ask('Please enter your email: ');

		$userdata['password'] = (new PasswordPrompter)->ask('Please enter password: ');

		$userdata['password2'] = (new PasswordPrompter)->ask('Please valid password: ');

		if ($userdata['password'] != $userdata['password2'])
		{
			throw new \InvalidArgumentException('ERROR: Password not matched.');
		}

		$userdata['groups'] = array(1);

		$userdata['block'] = 0;

		$userdata['sendEmail'] = 1;

		$user = new \JUser;

		if (!$user->bind($userdata))
		{
			throw new \RuntimeException($user->getError());
		}

		if (!$user->save())
		{
			throw new \RuntimeException($user->getError());
		}

		$userId = $user->id;

		// Save Super admin
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->update('#__user_usergroup_map')
			->set('group_id = 8')
			->where('user_id = ' . $userId);

		$db->setQuery($query)->execute();

		$this->out()->out('Create usr success.');

		return true;
	}
}
