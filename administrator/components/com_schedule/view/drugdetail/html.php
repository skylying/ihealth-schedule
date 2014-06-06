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

		$senderCid = $app->input->getString("senderCid");
		$senderCid = explode(",", $senderCid);

		$date = $app->input->get("date");

		if (empty($senderCid) || empty($senderCid[0]))
		{
			throw new \Exception("給我 sender !");
		}

		if (empty($date))
		{
			throw new \Exception("給我 date ! QAQ");
		}

		$this->data->tasks = $this->getTaskData($date, $senderCid);

		$taskCid = \JArrayHelper::getColumn($this->data->tasks, "id");

		$this->data->schedules = $this->getScheduleData($taskCid);

		$rxCid = \JArrayHelper::getColumn($this->data->tasks, "rx_id");

		$this->data->rxs = $this->getRxData($rxCid);

		$this->data->extras = $this->getDrugExtraData($taskCid);

		DataSortHelper::orderArrayByObjectId($this->data->rxs);

		DataSortHelper::orderArrayByObjectId($this->data->extras);

		parent::prepareData();
	}

	/**
	 * 取得 task 資料
	 *
	 * @param   string  $date
	 * @param   array   $senderCid
	 *
	 * @return  Data[]
	 */
	protected function getTaskData($date, $senderCid)
	{
		$taskMapper = new DataMapper(Table::TASKS);

		return $taskMapper->find(array("date" => $date, "sender" => $senderCid), array("sender DESC"));
	}

	/**
	 * 取得 schedule 資料
	 *
	 * @param   array  $taskCid
	 *
	 * @return  Data[]
	 */
	protected function getScheduleData($taskCid)
	{
		$schedulesMapper = new DataMapper(Table::SCHEDULES);

		return $schedulesMapper->find(array("task_id" => $taskCid), array("`type` DESC"));
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
	 * 取得處方資料
	 *
	 * @param   array  $rxCid
	 *
	 * @return  mixed
	 */
	protected function getRxData($rxCid)
	{
		$rxMapper = new DataMapper(Table::PRESCRIPTIONS);

		return $rxMapper->find($rxCid);
	}
}
