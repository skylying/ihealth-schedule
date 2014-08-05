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
use Windwalker\View\Html\GridView;
use Windwalker\View\Layout\FileLayout;
use Windwalker\Data\Data;

// No direct access
defined('_JEXEC') or die;

/**
 * Class SchedulesHtmlView
 *
 * @since 1.0
 */
class ScheduleViewSchedulesHtml extends GridView
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
	protected $name = 'schedules';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'schedule';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'schedules';

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
		$config['grid'] = array(
			// Some basic setting
			'option'    => 'com_schedule',
			'view_name' => 'schedule',
			'view_item' => 'schedule',
			'view_list' => 'schedules',

			// Column which we allow to drag sort
			'order_column'   => 'schedule.catid, schedule.ordering',

			// Table id
			'order_table_id' => 'scheduleList',

			// Ignore user access, allow all.
			'ignore_access'  => false
		);

		// Directly use php engine
		$this->engine = new PhpEngine;

		parent::__construct($model, $container, $config, $paths);
	}

	/**
	 * prepareData
	 *
	 * @return void
	 */
	protected function prepareData()
	{
		$app = JFactory::getApplication();
		$data = $this->getData();

		/** @var JForm $printForm */
		$data->printForm = $this->get('PrintForm');

		if ('report' == $this->getLayout())
		{
			$schedulesModel = $this->getModel('Schedules');
			$filter = $schedulesModel->getState()->get('report_filter');

			$ScheduleReport = new \Schedule\Helper\ScheduleReportHelper;
			$data->items = $ScheduleReport->getData($filter);

			return;
		}

		/** @var JForm $filterForm */
		$filterForm = $data->filterForm;

		// Get edit form fields
		$editFormFields = array();

		foreach (['date' => 'schedule.date_start', 'sender_id' => 'schedule.sender_id'] as $fieldName => $key)
		{
			$field = $filterForm->getField($key, 'filter');
			$field->group = '';
			$field->name = $fieldName;
			$field->id = 'edit_item_field_' . $fieldName;
			$field->onchange = '';

			if ('date' === $fieldName)
			{
				$field->dpBindEvent = '';
			}

			$editFormFields[$fieldName] = (string) $field->input;
		}

		$data->editFormFields = $editFormFields;

		$data->drugDetailForm = $this->get('DrugDetailFilterForm');

		$notifies = $this->get('Notifies');

		if (count($notifies) > 0)
		{
			$fileLayout = new FileLayout('schedule.schedules.notify');

			$notifyMessage = $fileLayout->render(new Data(['notifies' => $notifies]));

			$app->enqueueMessage($notifyMessage, 'warning');
		}
	}

	/**
	 * configToolbar
	 *
	 * @param array $buttonSet
	 * @param null  $canDo
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		$buttonSet = $this->configureReportToolbar($buttonSet);

		// Button 分藥註記
		$buttonSet['sorted_preview']['handler'] = function()
		{
			$html = <<<HTML
<a id="sorted-preview-button" href="#modal-sorted-preview" class="btn btn-small" data-toggle="modal">
	<span class="icon-list"></span> 打包表
</a>
HTML;
			$bar = JToolbar::getInstance('toolbar');
			$bar->appendButton('Custom', $html);
		};

		// Button 新增行政排程
		$buttonSet['add2']['handler'] = function()
		{
			$url = JRoute::_('index.php?option=com_schedule&task=schedule.edit.add&tmpl=component', false);

			$html = <<<HTML
<button id="add-new-item-button" class="btn btn-small btn-success">
	<span class="icon-new icon-white"></span> 新增行政排程
</button>
HTML;
			$js = <<<JAVASCRIPT
jQuery(function($)
{
	$('#add-new-item-button').click(function()
	{
		var node = $('#modal-add-new-item');

		node.on('show', function()
		{
			var frame = node.find('iframe');

			frame.attr("src", "");
			frame.attr("src", "{$url}");
		});

		node.modal({show:true});
	});
});
JAVASCRIPT;
			JFactory::getDocument()->addScriptDeclaration($js);

			$bar = JToolbar::getInstance('toolbar');
			$bar->appendButton('Custom', $html);
		};

		// Button 調整排程
		$buttonSet['edit']['handler'] = function()
		{
			$html = <<<HTML
<a id="edit-item-button" href="#modal-edit-item" class="btn btn-small" data-toggle="modal">
	<span class="icon-edit"></span> 排程調整
</a>
HTML;
			$bar = JToolbar::getInstance('toolbar');
			$bar->appendButton('Custom', $html);
		};

		return $buttonSet;
	}

	/**
	 * configureReportToolbar
	 *
	 * @param   array $buttonSet
	 *
	 * @return  mixed
	 */
	protected function configureReportToolbar($buttonSet)
	{
		if ('report' !== $this->getLayout())
		{
			$buttonSet['print']['handler'] = function()
			{
				$dHtml = <<<HTML
<a id="report-print-button" href="#modal-report-print" class="btn btn-small" data-toggle="modal">
	<span class="icon-print"></span> 處方統計表
</a>
HTML;
				$bar = JToolbar::getInstance('toolbar');
				$bar->appendButton('Custom', $dHtml);
			};

			// Get default delete button config from parent, but override the handler
			$parentButtonSet = parent::configureToolbar($buttonSet, $canDo = null);

			$buttonSet['delete'] = $parentButtonSet['delete'];
			$buttonSet['delete']['handler'] = function()
			{
				JToolbarHelper::deleteList('確定要刪除排程嗎？', 'schedules.state.delete', '刪除');
			};

			$buttonSet['delete']['access'] = true;

			return $buttonSet;
		}

		// If layout is report, disable those buttons
		foreach (['add2', 'add', 'publish', 'edit', 'unpublish',
			'checkin', 'batch', 'trash', 'delete', 'duplicate',
			'preferences', 'sorted_preview'] as $buttonName)
		{
			$buttonSet[$buttonName]['access'] = false;
		}

		$buttonSet['route']['handler'] = function()
		{
			$url = JRoute::_('index.php?option=com_schedule&view=schedules', false);

			$html = <<<HTML
<a id="edit-item-button" href="{$url}" class="btn btn-danger">
	<span class="glyphicon glyphicon-remove"></span> 取消列印
</a>
HTML;
			$bar = JToolbar::getInstance('toolbar');
			$bar->appendButton('Custom', $html);
		};

		return $buttonSet;
	}

	/**
	 * setTitle
	 *
	 * @param null   $title
	 * @param string $icons
	 *
	 * @return  void
	 */
	protected function setTitle($title = null, $icons = 'stack article')
	{
		if ('report' == $this->getLayout())
		{
			$title = '處方統計表';
		}

		parent::setTitle($title, $icons);
	}
}
