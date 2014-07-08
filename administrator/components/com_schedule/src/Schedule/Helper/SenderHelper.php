<?php

namespace Schedule\Helper;

use Schedule\Table\Table;
use Windwalker\Data\Data;

/**
 * Class SenderHelper
 *
 * @since 1.0
 */
class SenderHelper
{
	/**
	 * senderIsLogin
	 *
	 * @return  bool
	 */
	public static function isSenderLogin()
	{
		$isSender = static::isUserEqualSender();

		return $isSender ? true : false;
	}

	/**
	 * getSenderId
	 *
	 * @return  mixed
	 */
	public static function getSenderId()
	{
		$sender = static::getSenderData();

		return $sender->id;
	}

	/**
	 * isUserEqualSender
	 *
	 * @return  bool
	 */
	public static function isUserEqualSender()
	{
		$currentUserName = static::getLoginInfo()->name;

		$senderData = static::getSenderData();

		return $currentUserName == $senderData->name ? true : false;
	}

	/**
	 * getUserInfo
	 *
	 * @return  \JUser
	 */
	public static function getLoginInfo()
	{
		return \JFactory::getUser();
	}

	/**
	 * getSenderData
	 *
	 * @return  mixed
	 */
	public static function getSenderData()
	{
		$currentUserName = static::getLoginInfo()->name;

		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name')
			->from(Table::SENDERS)
			->where("name = '{$currentUserName}'");

		$data = $db->setQuery($query)->loadObject();

		if (!isset($data))
		{
			$data = new Data;
			$data->id = 0;
			$data->name = 0;
		}

		return $data;
	}
}
