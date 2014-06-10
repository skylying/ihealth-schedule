<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\DI\Container;
use Windwalker\Model\Model;
use Windwalker\View\Engine\PhpEngine;
use Windwalker\View\Html\EditView;
use Windwalker\Xul\XulEngine;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Table;
use Schedule\Helper\DataSortHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Schedule Drugdetails view
 *
 * @since 1.0
 */
class ScheduleViewDrugdetailHtml extends EditView
{
	/**
	 * The component prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'schedule';

	/**
	 * The component option name.
	 *
	 * @var  string
	 */
	protected $option = 'com_schedule';

	/**
	 * The text prefix for translate.
	 *
	 * @var  string
	 */
	protected $textPrefix = 'COM_SCHEDULE';

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $name = 'drugdetail';

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $viewItem = 'drugdetail';

	/**
	 * The list name.
	 *
	 * @var  string
	 */
	protected $viewList = 'drugdetails';

	/**
	 * Method to instantiate the view.
	 *
	 * @param Model            $model     The model object.
	 * @param Container        $container DI Container.
	 * @param array            $config    View config.
	 * @param SplPriorityQueue $paths     Paths queue.
	 */
	public function __construct(Model $model = null, Container $container = null, $config = array(), \SplPriorityQueue $paths = null)
	{
		$this->engine = new PhpEngine;

		parent::__construct($model, $container, $config, $paths);
	}

	/**
	 * Prepare data hook.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 */
	protected function prepareData()
	{
		$app = JFactory::getApplication();

		$senderIds = $app->input->getString("senderIds");
		$senderIds = explode(",", $senderIds);

		$this->data->date = $app->input->get("date");

		if (empty($senderIds) || empty($senderIds[0]))
		{
			throw new \Exception("給我 sender !");
		}

		if (empty($this->data->date))
		{
			throw new \Exception("給我 date !");
		}

		$tasks = $this->getTaskData($this->data->date, $senderIds);
		$taskIds = DataSortHelper::getArrayAccessColumn($tasks, "id");

		$db = JFactory::getDbo();
		$q  = $db->getQuery(true);

		$q->select("*, schedule.id AS id")
			->from(Table::SCHEDULES . " AS schedule")
			->join("LEFT", Table::TASKS . " AS task on schedule.task_id = task.id")
			->join("LEFT", Table::PRESCRIPTIONS . " AS rx on schedule.rx_id = rx.id")
			->where("schedule.task_id " . (new JDatabaseQueryElement('IN ()', $taskIds)))
			->order("schedule.institute_id desc");

		$schedules = $db->setQuery($q)->loadObjectList();

		$this->data->items = array();

		foreach ($tasks as $task)
		{
			$task->schedules = array();

			foreach ($schedules as $key => $schedule)
			{
				if ($task->id == $schedule->task_id)
				{
					$task->schedules[] = $schedule;

					// Optimization of the next foreach
					unset($schedules[$key]);
				}
			}

			if (! empty($task->schedules))
			{
				$this->data->items[] = $task;
			}
		}

		$this->data->extras = $this->getDrugExtraData($taskIds);

		parent::prepareData();
	}

	/**
	 * 取得額外分藥資料
	 *
	 * @param   array  $taskCid
	 *
	 * @return  Data[]
	 */
	protected function getDrugExtraData($taskCid)
	{
		$extraMapper = new DataMapper(Table::DRUG_EXTRA_DETAILS);

		return $extraMapper->find(array("task_id" => $taskCid));
	}

	/**
	 * 取得外送資料
	 *
	 * @param   string  $date
	 * @param   array   $senderIds
	 *
	 * @return  Data[]
	 */
	protected function getTaskData($date, $senderIds)
	{
		$taskMapper = new DataMapper(Table::TASKS);

		return $taskMapper->find(array("sender" => $senderIds, "date" => $date));
	}
}
