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
	 * Load customers
	 *
	 * @param   int  $memberId
	 *
	 * @return  \stdClass[]
	 */
	public static function loadCustomers($memberId)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from(static::RELATION_TABLE)
			->where('member_id=' . $memberId);

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * Load members
	 *
	 * @param   int  $customerId
	 *
	 * @return  \stdClass[]
	 */
	public static function loadMembers($customerId)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from(static::RELATION_TABLE)
			->where('customer_id=' . $customerId);

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * Add customers
	 *
	 * @param   int    $memberId
	 * @param   int[]  $customerIds
	 *
	 * @return  bool
	 */
	public static function connectCustomers($memberId, $customerIds)
	{
		if (empty($customerIds))
		{
			return true;
		}

		$customerIds = array_unique($customerIds);

		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->insert(static::RELATION_TABLE)
			->columns(array('member_id', 'customer_id'));

		foreach ($customerIds as $customerId)
		{
			$query->values($memberId . ', ' . $customerId);
		}

		$db->transactionStart();

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback();

			return false;
		}

		$db->transactionCommit();

		return true;
	}

	/**
	 * Add members
	 *
	 * @param   int    $customerId
	 * @param   int[]  $memberIds
	 *
	 * @return  bool
	 */
	public static function connectMembers($customerId, $memberIds)
	{
		if (empty($memberIds))
		{
			return true;
		}

		$memberIds = array_unique($memberIds);

		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->insert(static::RELATION_TABLE)
			->columns(array('customer_id', 'member_id'));

		foreach ($memberIds as $memberId)
		{
			$query->values($customerId . ', ' . $memberId);
		}

		$db->transactionStart();

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback();

			return false;
		}

		$db->transactionCommit();

		return true;
	}

	/**
	 * Delete customers
	 *
	 * @param   int  $memberId
	 *
	 * @return  bool
	 */
	public static function disconnectCustomers($memberId)
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->delete(static::RELATION_TABLE)
			->where('member_id = ' . $memberId);

		$db->transactionStart();

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback();

			return false;
		}

		$db->transactionCommit();

		return true;
	}

	/**
	 * Delete members
	 *
	 * @param   int  $customerId
	 *
	 * @return  bool
	 */
	public static function disconnectMembers($customerId)
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->delete(static::RELATION_TABLE)
			->where('customer_id = ' . $customerId);

		$db->transactionStart();

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback();

			return false;
		}

		$db->transactionCommit();

		return true;
	}

	/**
	 * updateCustomers
	 *
	 * @param   int    $memberId
	 * @param   int[]  $customerIds
	 *
	 * @return  bool
	 */
	public static function updateCustomers($memberId, $customerIds = array())
	{
		if (empty($customerIds))
		{
			return true;
		}

		$app = \JFactory::getApplication();

		if (!static::disconnectCustomers($memberId))
		{
			$app->enqueueMessage('COM_SCHEDULE_MEMBER_DISCONNECT_MAPPING_FAILED', 'error');

			return false;
		}

		if (!static::connectCustomers($memberId, $customerIds))
		{
			$app->enqueueMessage('COM_SCHEDULE_MEMBER_CONNECT_MAPPING_FAILED', 'error');

			return false;
		}

		return true;
	}

	/**
	 * updateMembers
	 *
	 * @param   int    $customerId
	 * @param   int[]  $memberIds
	 *
	 * @return  bool
	 */
	public static function updateMembers($customerId, $memberIds = array())
	{
		if (empty($memberIds))
		{
			return true;
		}

		$app = \JFactory::getApplication();

		if (!static::disconnectMembers($customerId))
		{
			$app->enqueueMessage('COM_SCHEDULE_CUSTOMER_DISCONNECT_MAPPING_FAILED', 'error');

			return false;
		}

		if (!static::connectMembers($customerId, $memberIds))
		{
			$app->enqueueMessage('COM_SCHEDULE_CUSTOMER_CONNECT_MAPPING_FAILED', 'error');

			return false;
		}

		return true;
	}
}
