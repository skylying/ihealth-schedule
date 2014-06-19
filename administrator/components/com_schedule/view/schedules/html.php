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
use Windwalker\Xul\XulEngine;

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
		$data = $this->getData();

		if ('report' == $this->getLayout())
		{
			$schedulesModel = $this->getModel('Schedules');
			$filter = $schedulesModel->getState()->get('report_filter');

			$ScheduleReport = new \Schedule\Helper\ScheduleReportHelper;
			$data->items = $ScheduleReport->getData($filter);

			$data->printForm = $this->get('PrintForm');

			return;
		}

		/** @var JForm $filterForm */
		$filterForm = $data->filterForm;

		// Get edit form fields
		$editFormFields = array();

		foreach (['date' => 'schedule.date_start', 'sender_id' => 'route.sender_id'] as $fieldName => $key)
		{
			$field = $filterForm->getField($key, 'filter');
			$field->group = '';
			$field->name = $fieldName;
			$field->id = 'edit_item_field_' . $fieldName;
			$field->onchange = '';

			$editFormFields[$fieldName] = (string) $field->input;
		}

		$data->editFormFields = $editFormFields;

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
<button class="btn btn-small" onclick="Joomla.submitbutton('schedules.report')">
	<span class="glyphicon glyphicon-print"></span> 列印排程統計報表
</button>
HTML;
				$bar = JToolbar::getInstance('toolbar');
				$bar->appendButton('Custom', $dHtml);
			};

			return $buttonSet;
		}

		// If layout is report do this
		$buttonSet['add2']['access']        = false;
		$buttonSet['add']['access']         = false;
		$buttonSet['publish']['access']     = false;
		$buttonSet['edit']['access']        = false;
		$buttonSet['unpublish']['access']   = false;
		$buttonSet['checkin']['access']     = false;
		$buttonSet['batch']['access']       = false;
		$buttonSet['trash']['access']       = false;
		$buttonSet['delete']['access']      = false;
		$buttonSet['duplicate']['access']   = false;
		$buttonSet['preferences']['access'] = false;

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
}
