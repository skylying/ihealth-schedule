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
	 * getDefaultSender
	 *
	 * @return  array
	 */
	public static function getDefaultSender()
	{
		$defaultSender = \JComponentHelper::getParams('com_schedule')->get('sender.default_sender');

		$defaultSenderFromConfig = (strpos($defaultSender, '-') !== false) ? $defaultSender : '0-0';

		list($id, $name) = explode('-', $defaultSenderFromConfig);

		return array(
			'id' => $id,
			'sender' => $name
		);
	}

	/**
	 * checkSender, if user belongs to sender, return an array of sender information, else return false
	 *
	 * @return  array|bool
	 */
	public static function checkSender()
	{
		$user = \JFactory::getUser();

		// Find user in sender table
		$result = (array) (new DataMapper(Table::SENDERS))->findOne(['user_id' => $user->id]);

		if (empty($result))
		{
			return false;
		}

		return $result;
	}
}
