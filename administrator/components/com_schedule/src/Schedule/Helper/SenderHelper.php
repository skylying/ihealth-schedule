<?php

namespace Schedule\Helper;

use Schedule\Table\Table;
use Windwalker\Data\Data;
use Windwalker\Joomla\DataMapper\DataMapper;

/**
 * Class SenderHelper
 *
 * @since 1.0
 */
class SenderHelper
{
	/**
	 * isSenderLogin
	 *
	 * @return  bool
	 */
	public static function isSenderLogin()
	{
		$sender = static::sender();

		return ! $sender->isNull();
	}

	/**
	 * getSenderId
	 *
	 * @return  int
	 */
	public static function getSenderId()
	{
		$sender = static::sender();

		return $sender->id;
	}

	/**
	 * sender
	 *
	 * @return  mixed
	 */
	public static function sender()
	{
		$user = \JFactory::getUser();

		return (new DataMapper(Table::SENDERS))->findOne(array('name' => $user->name));
	}

	/**
	 * getDefaultSender
	 *
	 * @return  array
	 */
	public static function getDefaultSender()
	{
		$defaultSender = \JComponentHelper::getParams('com_schedule')->get("icrm.default_sender");

		$defaultSenderFromConfig = (strpos($defaultSender, '-') !== false) ? $defaultSender : '0-0';

		list($id, $name) = explode('-', $defaultSenderFromConfig);

		return array(
			'id' => $id,
			'sender' => $name
		);
	}
}
