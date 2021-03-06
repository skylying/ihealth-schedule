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
		$senderIds = $this->handleFilterVar('senderIds');
		$this->data->date_start = $this->handleFilterVar('date_start');
		$this->data->date_end = $this->handleFilterVar('date_end');
		$this->data->weekday = $this->handleFilterVar('weekday');

		// Get schedules
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

				// 行政排程
				case "discuss":
				case "speech":
				case "collect":
				case "visit":
				case "evaluation":
				case "other":
					$items[$senderId]['admin'][] = array(
						'date' => $schedule->date,
						'type' => JText::_('COM_SCHEDULE_SCHEDULE_FIELD_TYPE_' . $schedule->type) ,
						'to' => empty($schedule->name) ? $schedule->institute_title : $schedule->name,
						'status' => $schedule->status,
						'note' => $schedule->note,
					);
			}
		}

		$this->data->items = $items;

		$this->preparePrintData();

		$filterData['senderIds']  = $senderIds;
		$filterData['date_start'] = $this->data->date_start;
		$filterData['date_end']   = $this->data->date_end;
		$filterData['weekday']    = $this->data->weekday;

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

		$select = [
			'* ',
			'`schedule`.`id` AS id',
			'`schedule`.`type` AS type',
			'`schedule`.`modified_by` AS modified_by',
			'`schedule`.`params` AS params',
			'`schedule`.`status` AS status',
			'`schedule`.`note` AS note',
			'`task`.`sender` AS sender',
			'`schedule`.`institute_id` AS institute_id',
			'`task`.`id` AS task_id',
			'`customer`.`need_split` AS need_split',
			'`rx`.`created` AS created',
		];

		$q->select($select)
			->from(Table::SCHEDULES . " AS schedule")
			->join("LEFT", Table::TASKS . " AS task on schedule.task_id = task.id")
			->join("LEFT", Table::PRESCRIPTIONS . " AS rx on schedule.rx_id = rx.id")
			->join("LEFT", Table::CUSTOMERS . " AS customer on schedule.customer_id = customer.id")
			->where("task.sender " . (new JDatabaseQueryElement('IN ()', $senderIds)))
			->where("task.date >= " . $q->quote($this->data->date_start));

		if ($this->data->weekday != '*')
		{
			$q->where("schedule.weekday = " . $q->quote($this->data->weekday));
		}
		else
		{
			$this->data->weekday = "";
		}

		$q->where('schedule.status != "delivered"')
			->order("rx.floor DESC, schedule.drug_empty_date ASC");

		if (!empty($this->data->date_end))
		{
			$q->where("task.date <= " . $q->quote($this->data->date_end));
		}

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
		$buttonSet = parent::configureToolbar($buttonSet, $canDo);

		unset($buttonSet['save2new']);
		unset($buttonSet['save2copy']);

		$buttonSet['print']['handler'] = function ()
		{
			$html = <<<HTML
<button class="btn btn-info" onclick="jQuery('input[name=save-and-print]').val('1');Joomla.submitbutton('drugdetail.edit.apply');">
	<span class="glyphicon glyphicon-print"></span> 儲存&列印
</button>
HTML;
			$bar  = JToolbar::getInstance('toolbar');

			$bar->appendButton('custom', $html);
		};

		return $buttonSet;
	}

	/**
	 * preparePrintData
	 *
	 * @return  void
	 */
	public function preparePrintData()
	{
		$app = JFactory::getApplication();

		$isSaveAndPrint = $app->getUserState('save-and-print');

		// SaveAndPrint state has only used once
		$app->setUserState('save-and-print', null);

		$data = $this->data;

		$data->print = $isSaveAndPrint;
	}

	/**
	 * handleFilterVar (simulate populate state)
	 *
	 * @param   string $key
	 *
	 * @return  mixed
	 */
	protected function handleFilterVar($key)
	{
		$app = JFactory::getApplication();

		$var = $app->input->getVar($key);
		$varInState = $app->getUserState('drugdetail.filter.' . $key);

		if (!$var && !$varInState)
		{
			$app->redirect(JRoute::_('index.php?option=com_schedule&view=schedules', false), JText::_('COM_SCHEDULE_DRUGDETAIL_FILTER_' . $key . '_NOT_EXIST'), 'warning');
		}
		elseif ($var && $varInState)
		{
			$app->setUserState('drugdetail.filter.' . $key, $var);

			return $var;
		}
		elseif (!$var && $varInState)
		{
			return $varInState;
		}
		else
		{
			$app->setUserState('drugdetail.filter.' . $key, $var);

			return $var;
		}
	}
}
