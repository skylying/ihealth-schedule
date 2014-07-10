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
		return (static::getSenderId() == 0 ? false : true );
	}

	/**
	 * getSenderId
	 *
	 * @return  int
	 */
	public static function getSenderId()
	{
		$user = \JFactory::getUser();
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('id, name')
			->from(Table::SENDERS)
			->where("name = '{$user->name}'");

		$data = $db->setQuery($query)->loadObject();

		if (!$data)
		{
			$data = new DataMapper(Table::SENDERS);
			$data->id = 0;
		}

		return $data->id;
	}
}
