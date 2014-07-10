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
 * Class TasksHtmlView
 *
 * @since 1.0
 */
class ScheduleViewTasksHtml extends GridView
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
	protected $name = 'tasks';

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
		$config['grid'] = array(
			// Some basic setting
			'option'    => 'com_schedule',
			'view_name' => 'task',
			'view_item' => 'task',
			'view_list' => 'tasks',

			// Column which we allow to drag sort
			'order_column'   => 'task.catid, task.ordering',

			// Table id
			'order_table_id' => 'taskList',

			// Ignore user access, allow all.
			'ignore_access'  => false
		);

		// Directly use php engine
		$this->engine = new PhpEngine;

		parent::__construct($model, $container, $config, $paths);
	}

	/**
	 * render
	 *
	 * @return void
	 */
	protected function prepareData()
	{
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
		// Get default button set.
		$buttonSet = parent::configureToolbar($buttonSet, $canDo);

		$buttonSet['edit']['access'] = false;
		$buttonSet['duplicate']['access'] = false;
		$buttonSet['publish']['access'] = false;
		$buttonSet['unpublish']['access'] = false;
		$buttonSet['checkin']['access'] = false;
		$buttonSet['batch']['access'] = false;
		$buttonSet['delete']['access'] = false;
		$buttonSet['add']['access'] = false;
		$buttonSet['trash']['access'] = false;

		// Add custom controller redirect to routes overview layout
		$senderLoginStat = \Schedule\Helper\SenderHelper::isSenderLogin();

		if (!$senderLoginStat)
		{
			$buttonSet['route']['handler'] = function()
			{
				$html = <<<HTML
<button class="btn btn-info btn-small" onclick="Joomla.submitbutton('tasks.redirect')">
	<span class="glyphicon glyphicon-random"></span> 路線管理
</button>
HTML;
				$bar = JToolbar::getInstance('toolbar');

				$bar->appendButton('Custom', $html);
			};
		};

		$buttonSet['completeDelivery']['handler'] = function(){
			JToolbarHelper::custom('tasks.state.delivery', 'checkin', 'checkin', '完成外送');
		};

		$buttonSet['waitDelivery']['handler'] = function(){
			JToolbarHelper::custom('tasks.state.undelivery', 'pause', 'pause', '改回待外送');
		};

		return $buttonSet;
	}
}
