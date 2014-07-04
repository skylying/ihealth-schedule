<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\DI\Container;
use Windwalker\Model\Model;
use Windwalker\View\Engine\PhpEngine;
use Windwalker\View\Html\EditView;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Table\Table;
use Schedule\Table\Collection as TableCollection;
use Schedule\Helper\TaskHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class TasksHtmlView
 *
 * @since 1.0
 */
class ScheduleViewTaskHtml extends EditView
{
	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'schedule';

	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option = 'com_schedule';

	/**
	 * Property textPrefix.
	 *
	 * @var string
	 */
	protected $textPrefix = 'COM_SCHEDULE';

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'task';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'task';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'tasks';

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
	 * Prepare render hook.
	 *
	 * @return  void
	 */
	protected function prepareRender()
	{
		parent::prepareRender();
	}

	/**
	 * Set title of this page.
	 *
	 * @param string $title Page title.
	 * @param string $icons Title icon.
	 *
	 * @return  void
	 */
	protected function setTitle($title = null, $icons = 'pencil-2')
	{
		if ('print' === $this->getLayout())
		{
			parent::setTitle('預覽列印', $icons);

			return;
		}

		parent::setTitle($title, $icons);
	}

	/**
	 * prepareData
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
		parent::prepareData();

		// Prepare print data
		if ('print' === $this->getLayout())
		{
			$data = $this->getData();

			$schedules = (new DataMapper(Table::SCHEDULES))->find(['task_id' => $data->item->id]);

			$data->item->schedules = $this->getSummarizeScheduleData(iterator_to_array($schedules));

			$data->item->instituteQuntity = count($data->item->schedules['institutes']);
			$data->item->customerQuntity = count($data->item->schedules['customers']);
			$data->item->totalQuntity = $data->item->instituteQuntity + $data->item->customerQuntity;
		}
	}

	/**
	 * configToolbar
	 *
	 * @param   array   $buttonSet
	 * @param   object  $canDo
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		if ('print' === $this->getLayout())
		{
			return $this->configurePrintToolbar();
		}

		$buttonSet = parent::configureToolbar($buttonSet, $canDo);

		// Set print button
		$buttonSet['print']['handler'] = function()
		{
			$id = JFactory::getApplication()->input->get('id', 0);

			$url = JRoute::_('index.php?option=com_schedule&view=task&layout=print&id=' . $id);

			$html = <<<HTML
<a href="{$url}" class="btn btn-info btn-small">
	<span class="glyphicon glyphicon-print"></span> 列印
</a>
HTML;
			JToolbar::getInstance('toolbar')->appendButton('Custom', $html);
		};

		$buttonSet['save2new']['access'] = false;
		$buttonSet['save2copy']['access'] = false;

		return $buttonSet;
	}

	/**
	 * configurePrintToolbar
	 *
	 * @param   array  $buttonSet
	 *
	 * @return  array
	 */
	protected function configurePrintToolbar(array $buttonSet = array())
	{
		// Set return to list page button
		$buttonSet['listPage']['handler'] = function()
		{
			$url = JRoute::_('index.php?option=com_schedule&view=tasks');

			$html = <<<HTML
<a href="{$url}" class="btn btn-small">
	<span class="glyphicon glyphicon-remove"></span> 取消回清單頁
</a>
HTML;
			JToolbar::getInstance('toolbar')->appendButton('Custom', $html);
		};

		// Set print button
		$buttonSet['print']['handler'] = function()
		{
			$id = JFactory::getApplication()->input->get('id', 0);

			$url = JRoute::_('index.php?option=com_schedule&view=task&layout=print&tmpl=component&id=' . $id);

			$html = <<<HTML
<a href="{$url}" class="btn btn-info btn-small" target="_blank">
	<span class="glyphicon glyphicon-print"></span> 列印
</a>
HTML;
			JToolbar::getInstance('toolbar')->appendButton('Custom', $html);
		};

		return $buttonSet;
	}

	/**
	 * getSummarizeScheduleData
	 *
	 * TODO: 加入額外藥品資料
	 *
	 * @param   array  $schedules
	 *
	 * @return  array
	 */
	private function getSummarizeScheduleData(array $schedules)
	{
		$data = [
			'institutes' => [],
			'customers' => [],
			'others' => [],
		];

		foreach ($schedules as $schedule)
		{
			if ($schedule['institute_id'] > 0)
			{
				// Summarize resident customers
				if (! isset($data['institutes'][$schedule['institute_id']]))
				{
					$row = $this->getInitSchedule($schedule);
					$row['title'] = $schedule['institute_title'];

					$instituteTable = TableCollection::loadTable('Institute', $schedule['institute_id']);

					$note = trim($instituteTable->note);

					if (! empty($note))
					{
						$row['notes'][] = [
							'type' => '',
							'message' => $note,
						];
					}

					$row['phones'][] = $instituteTable->tel;

					$extraExpenses = TaskHelper::getInstituteExtraExpenses($schedule['task_id'], $schedule['institute_id']);
					$totalExtraExpense = array_reduce(
						$extraExpenses,
						function($carry, $item)
						{
							return $carry += $item->price;
						}
					);

					if ($totalExtraExpense > 0)
					{
						$row['extraExpenses'] = '加購總額: $' . $totalExtraExpense;
					}

					$data['institutes'][$schedule['institute_id']] = $row;
				}

				$this->summarizeSchedules($schedule, $data['institutes'][$schedule['institute_id']]);
			}
			elseif ($schedule['customer_id'] > 0)
			{
				// Summarize individual customers
				if (! isset($data['customers'][$schedule['customer_id']]))
				{
					$row = $this->getInitSchedule($schedule);
					$row['title'] = $schedule['customer_name'];

					$data['customers'][$schedule['customer_id']] = $row;
				}

				$this->summarizeSchedules($schedule, $data['customers'][$schedule['customer_id']]);
			}
			else
			{
				$row = $this->getInitSchedule($schedule);
				$row['title'] = '行政排程';

				$data['others'][$schedule['id']] = $row;

				$this->summarizeSchedules($schedule, $data['others'][$schedule['id']]);
			}
		}

		return $data;
	}

	/**
	 * getInitSchedule
	 *
	 * @param   array  $schedule
	 *
	 * @return  array
	 */
	private function getInitSchedule($schedule)
	{
		$phones = [];

		// Get phone information
		foreach (['mobile', 'tel_home', 'tel_office'] as $key)
		{
			$phone = trim($schedule[$key]);

			if (! empty($phone))
			{
				$phones[] = $phone;
			}
		}

		return [
			'title' => '',
			'address' => $schedule['city'] . $schedule['area'] . $schedule['address'],
			'notes' => [],
			'quantity' => 0,
			'phones' => $phones,
			'ices' => [],
			'expenses' => [],
			'extraExpenses' => '',
			'session' => JText::_('COM_SCHEDULE_SEND_SESSION_' . $schedule['session']),
		];
	}

	/**
	 * summarizeSchedules
	 *
	 * @param   array  $schedule
	 * @param   array  &$row
	 *
	 * @return  void
	 */
	private function summarizeSchedules($schedule, &$row)
	{
		if (empty($row))
		{
			return;
		}

		++$row['quantity'];

		if ($schedule['ice'])
		{
			$row['ices'][] = [
				'drug_empty_date' => str_replace('-', '/', substr($schedule['drug_empty_date'], 5)),
				'customer_name' => $schedule['customer_name'],
			];
		}

		if ($schedule['expense'])
		{
			$row['expenses'][] = [
				'customer_name' => $schedule['customer_name'],
				'price' => $schedule['price'],
			];
		}

		if ('cancel_reject' === $schedule['status'])
		{
			$row['notes'][] = [
				'type' => '處方箋退單',
				'message' => $schedule['customer_name'],
			];
		}

		switch ($schedule['type'])
		{
			case 'discuss':
			case 'speech':
			case 'collect':
			case 'visit':
			case 'other':
				$row['notes'][] = [
					'type' => JText::_('COM_SCHEDULE_SCHEDULE_FIELD_TYPE_' . $schedule['type']),
					'message' => $schedule['note'],
				];
				break;
		}
	}
}
