<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper\Mapping;

use Schedule\Table\Table;

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

		$query->select('customer.*, map.member_id')
			->from(static::RELATION_TABLE . ' AS map')
			->leftJoin(Table::CUSTOMERS . ' AS customer ON map.customer_id=customer.id')
			->where('map.member_id=' . $memberId);

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

		$query->select('member.*, map.customer_id')
			->from(static::RELATION_TABLE . ' AS map')
			->leftJoin(Table::MEMBERS . ' AS member ON map.member_id=member.id')
			->where('map.customer_id=' . $customerId);

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * Add customers
	 *
	 * @param   int   $memberId
	 * @param   int[] $customerIds
	 *
	 * @throws  \Exception
	 * @return  bool
	 */
	public static function connectCustomers($memberId, array $customerIds)
	{
		if (empty($customerIds))
		{
			return true;
		}

		$customerIds = array_unique($customerIds);

		$app = \JFactory::getApplication();
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->insert(static::RELATION_TABLE)
			->columns(array('member_id', 'customer_id'));

		foreach ($customerIds as $customerId)
		{
			$query->values($memberId . ', ' . $customerId);
		}

		$db->transactionStart(true);

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback(true);

			$app->enqueueMessage($e->getMessage());

			if (JDEBUG)
			{
				throw $e;
			}

			return false;
		}

		$db->transactionCommit(true);

		return true;
	}

	/**
	 * Add members
	 *
	 * @param   int   $customerId
	 * @param   int[] $memberIds
	 *
	 * @throws  \Exception
	 * @return  bool
	 */
	public static function connectMembers($customerId, array $memberIds)
	{
		if (empty($memberIds))
		{
			return true;
		}

		$memberIds = array_unique($memberIds);

		$app = \JFactory::getApplication();
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->insert(static::RELATION_TABLE)
			->columns(array('customer_id', 'member_id'));

		foreach ($memberIds as $memberId)
		{
			$query->values($customerId . ', ' . $memberId);
		}

		$db->transactionStart(true);

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback(true);

			$app->enqueueMessage($e->getMessage());

			if (JDEBUG)
			{
				throw $e;
			}

			return false;
		}

		$db->transactionCommit(true);

		return true;
	}

	/**
	 * Delete customers
	 *
	 * @param   int $memberId
	 *
	 * @throws  \Exception
	 * @return  bool
	 */
	public static function disconnectCustomers($memberId)
	{
		$app = \JFactory::getApplication();
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->delete(static::RELATION_TABLE)
			->where('member_id = ' . $memberId);

		$db->transactionStart(true);

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback(true);

			$app->enqueueMessage($e->getMessage());

			if (JDEBUG)
			{
				throw $e;
			}

			return false;
		}

		$db->transactionCommit(true);

		return true;
	}

	/**
	 * Delete members
	 *
	 * @param   int $customerId
	 *
	 * @throws  \Exception
	 * @return  bool
	 */
	public static function disconnectMembers($customerId)
	{
		$app = \JFactory::getApplication();
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->delete(static::RELATION_TABLE)
			->where('customer_id = ' . $customerId);

		$db->transactionStart(true);

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback(true);

			$app->enqueueMessage($e->getMessage());

			if (JDEBUG)
			{
				throw $e;
			}

			return false;
		}

		$db->transactionCommit(true);

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
	public static function updateCustomers($memberId, array $customerIds = array())
	{
		if (empty($customerIds))
		{
			return true;
		}

		$app = \JFactory::getApplication();

		if (!static::disconnectCustomers($memberId))
		{
			$app->enqueueMessage('更新客戶對應時，刪除關聯失敗', 'error');

			return false;
		}

		if (!static::connectCustomers($memberId, $customerIds))
		{
			$app->enqueueMessage('更新客戶對應時，加入關聯失敗', 'error');

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
	public static function updateMembers($customerId, array $memberIds = array())
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

	/**
	 * createRelation
	 *
	 * @param int $memberId
	 * @param int $customerId
	 *
	 * @throws  \Exception
	 * @return  bool
	 */
	public static function createRelation($memberId, $customerId)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id')
			->from(self::RELATION_TABLE)
			->where('member_id = ' . $memberId)
			->where('customer_id = ' . $customerId);

		$id = (int) $db->setQuery($query)->loadColumn();

		// Do nothing when relation is already exists
		if ($id > 0)
		{
			return true;
		}

		$app = \JFactory::getApplication();

		$query->clear()
			->insert(static::RELATION_TABLE)
			->columns(array('member_id', 'customer_id'))
			->values($memberId . ', ' . $customerId);

		$db->transactionStart(true);

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback(true);

			$app->enqueueMessage($e->getMessage());

			if (JDEBUG)
			{
				throw $e;
			}

			return false;
		}

		$db->transactionCommit(true);

		return true;
	}
}
