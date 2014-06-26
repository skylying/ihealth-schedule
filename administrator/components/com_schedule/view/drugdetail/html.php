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

		$senderIds = $app->input->getVar('senderIds', array());

		$this->data->date = $app->input->get("date");

		if (empty($senderIds))
		{
			throw new \Exception("給我 sender !");
		}

		if (empty($this->data->date))
		{
			throw new \Exception("給我 date !");
		}

		$schedules = $this->getRelatedSchedules($senderIds);
		$taskIds   = \JArrayHelper::getColumn($schedules, "task_id");
		$this->data->extras = $this->getDrugExtraDataSet($taskIds);

		$items = array();

		foreach ($schedules as $schedule)
		{
			$senderId = intval($schedule->sender);

			if (! isset($items[$senderId]))
			{
				$items[$senderId] = array();
				$items[$senderId]['institutes'] = array();
				$items[$senderId]['individuals'] = array();
				$items[$senderId]['name'] = $schedule->sender_name;
				$items[$senderId]['task_id'] = $schedule->task_id;
			}

			$instituteId = intval($schedule->institute_id);

			switch ($schedule->type)
			{
				case "individual":
					// 散客
					$items[$senderId]['individuals'][] = $schedule;
				break;

				case "resident":
					if (! isset($items[$senderId]['institutes'][$instituteId]))
					{
						$items[$senderId]['institutes'][$instituteId] = array();
						$items[$senderId]['institutes'][$instituteId]['schedule'] = array();
						$items[$senderId]['institutes'][$instituteId]['extra'] = array();
					}

					$items[$senderId]['institutes'][$instituteId]['schedules'][] = $schedule;
				break;
			}
		}

		$this->data->items = $items;

		$filterData['senderIds'] = $app->input->getVar("senderIds", array());
		$filterData['date']      = $app->input->get("date", "");

		$this->data->filterForm = $this->get('FilterForm', null, array($filterData));

		parent::prepareData();
	}

	/**
	 * 取得額外分藥資料
	 *
	 * @param   array  $taskIds
	 *
	 * @return  Data[]
	 *
	 * return 詳細形式如下
	 *
	 * ```php
	 * array(
	 *     1 => array(               // Task id
	 *         2 => array(           // Institute id
	 *             Data(
	 *                 "id"           => 1,
	 *                 "task_id"      => 1,
	 *                 "price"        => 888.88,
	 *                 "institute_id" => 2,
	 *                 "ice"          => 1,
	 *                 "sorted"       => 1,
	 *             )
	 *         )
	 *     ),
	 *     2 => array(
	 *         array(
	 *             ...
	 *         )
	 *     ),
	 * )
	 * ```
	 */
	protected function getDrugExtraDataSet($taskIds)
	{
		$extraMapper = new DataMapper(Table::DRUG_EXTRA_DETAILS);

		$extras = $extraMapper->find(array("task_id" => $taskIds));

		$returnVal = array();

		foreach ($extras as $extra)
		{
			if (! isset($returnVal[$extra->task_id]))
			{
				$returnVal[$extra->task_id] = array();
			}

			if (! isset($returnVal[$extra->task_id][$extra->institute_id]))
			{
				$returnVal[$extra->task_id][$extra->institute_id] = array();
			}

			$returnVal[$extra->task_id][$extra->institute_id][] = $extra;
		}

		return $returnVal;
	}

	/**
	 * Get Related Schedules
	 *
	 * @param   array  $senderIds
	 *
	 * @return  stdClass[]
	 */
	protected function getRelatedSchedules($senderIds = array())
	{
		$db = JFactory::getDbo();
		$q  = $db->getQuery(true);

		$q->select("*, schedule.id AS id, schedule.`type` AS `type`, task.sender AS sender, schedule.institute_id AS institute_id, task.id AS task_id")
			->from(Table::SCHEDULES . " AS schedule")
			->join("LEFT", Table::TASKS . " AS task on schedule.task_id = task.id")
			->join("LEFT", Table::PRESCRIPTIONS . " AS rx on schedule.rx_id = rx.id")
			->where("task.sender " . (new JDatabaseQueryElement('IN ()', $senderIds)))
			->where("task.date = " . $q->quote($this->data->date))
			->order("schedule.institute_id DESC")
			->order("task.sender DESC");

		return $db->setQuery($q)->loadObjectList();
	}

	/**
	 * ConfigureToolbar
	 *
	 * @param array $buttonSet
	 * @param null  $canDo
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		$returnVal = parent::configureToolbar($buttonSet, $canDo);

		unset($returnVal['save2new']);
		unset($returnVal['save2copy']);

		return $returnVal;
	}
}
