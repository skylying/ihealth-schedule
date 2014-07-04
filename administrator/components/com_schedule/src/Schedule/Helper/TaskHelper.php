<?php

namespace Schedule\Helper;

use Schedule\Table\Table;

/**
 * Class TaskHelper
 *
 * @since 1.0
 */
class TaskHelper
{
	/**
	 * getInstituteExtraExpenses
	 *
	 * @param   int  $taskId
	 * @param   int  $instituteId
	 *
	 * @return  \stdClass[]
	 */
	public static function getInstituteExtraExpenses($taskId, $instituteId)
	{
		$taskId = (int) $taskId;
		$instituteId = (int) $instituteId;
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id, price, ice, sorted')
			->from(Table::DRUG_EXTRA_DETAILS)
			->where('task_id = ' . $taskId)
			->where('institute_id = ' . $instituteId);

		return $db->setQuery($query)->loadObjectList();
	}
}
