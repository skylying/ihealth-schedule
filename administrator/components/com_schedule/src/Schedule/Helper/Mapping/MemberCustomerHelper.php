<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper\Mapping;

/**
 * Class MemberCustomerHelper
 *
 * @since 1.0
 */
class MemberCustomerHelper
{
	const RELATION_TABLE = '#__schedule_customer_member_maps';

	/**
	 * updateMappingConnections
	 *
	 * @param       $mid
	 * @param array $cids
	 *
	 * @return  bool
	 */
	public static function updateMappingConnections( $mid, $cids = array() )
	{
		if (empty($cids))
		{
			return true;
		}

		$app = \JFactory::getApplication();

		if (!static::disconnectCustomers($mid))
		{
			$app->enqueueMessage('COM_SCHEDULE_MEMBER_DISCONNECT_MAPPING_FAILED', 'error');

			return false;
		}

		if (!static::connectCustomers($mid, $cids))
		{
			$app->enqueueMessage('COM_SCHEDULE_MEMBER_CONNECT_MAPPING_FAILED', 'error');

			return false;
		}

		return true;
	}

	/**
	 * deleteCustomers
	 *
	 * @param int $mid
	 *
	 * @return  bool
	 */
	public static function disconnectCustomers($mid)
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->delete(static::RELATION_TABLE)
			->where('member_id = ' . $mid);

		if (!$db->setQuery($query)->execute())
		{
			return false;
		}

		return true;
	}

	/**
	 * addTypes
	 *
	 * @param int   $mid
	 * @param int[] $cids
	 *
	 * @return  bool
	 */
	public static function connectCustomers($mid, $cids)
	{
		if (empty($cids))
		{
			return true;
		}

		$cids = array_unique($cids);

		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->insert(static::RELATION_TABLE)
			->columns(array('member_id', 'customer_id'));

		array_map(
			function ($cid) use ($query, $mid)
			{
				$query->values($mid . ', ' . $cid);
			},
			$cids
		);

		if (!$db->setQuery($query)->execute())
		{
			return false;
		}

		return true;
	}
}
