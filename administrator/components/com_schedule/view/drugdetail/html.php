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

		$this->data->date = $app->input->get("date");

		if (empty($senderCid) || empty($senderCid[0]))
		{
			throw new \Exception("給我 sender !");
		}

		if (empty($this->data->date))
		{
			throw new \Exception("給我 date ! QAQ");
		}

		$db = JFactory::getDbo();
		$q  = $db->getQuery(true);

		$q->select("*, schedule.id AS id")
			->from(Table::SCHEDULES . " AS schedule")
			->join("LEFT", Table::TASKS . " AS task on schedule.task_id = task.id")
			->join("LEFT", Table::PRESCRIPTIONS . " AS rx on schedule.rx_id = rx.id")
			->where("task.sender in (" . implode(",", $senderCid) . ")")
			->where("task.date = " . $q->quote($this->data->date))
			->order("schedule.institute_id desc")
			->order("task.sender desc");

		$this->data->items = $db->setQuery($q)->loadObjectList();

		$taskCid = \JArrayHelper::getColumn($this->data->items, "task_id");

		$this->data->extras = $this->getDrugExtraData($taskCid);

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
}
