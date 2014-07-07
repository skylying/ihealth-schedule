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
 * Class RoutesHtmlView
 *
 * @since 1.0
 */
class ScheduleViewRoutesHtml extends GridView
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
	protected $name = 'routes';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'route';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'routes';

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
			'view_name' => 'route',
			'view_item' => 'route',
			'view_list' => 'routes',

			// Column which we allow to drag sort
			'order_column'   => 'route.catid, route.ordering',

			// Table id
			'order_table_id' => 'routeList',

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
	 * @param array $buttonSet
	 * @param null  $canDo
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		// Get default button set.
		$buttonSet = parent::configureToolbar($buttonSet, $canDo);

		// In debug mode, we remove trash button but use delete button instead.
		if (JDEBUG)
		{
			$buttonSet['trash']['access'] = false;
			$buttonSet['delete']['access'] = true;
		}

		// Remove all buttons we do not need
		$buttonSet['edit']['access']        = false;
		$buttonSet['duplicate']['access']   = false;
		$buttonSet['publish']['access']     = false;
		$buttonSet['unpublish']['access']   = false;
		$buttonSet['checkin']['access']     = false;
		$buttonSet['delete']['access']      = false;
		$buttonSet['trash']['access']       = false;
		$buttonSet['batch']['access']       = false;
		$buttonSet['preferences']['access'] = false;

		$buttonSet['add']['args'] = array($this->viewItem . '.edit.save', 'title' => '儲存變更');

		$buttonSet['add2'] = $buttonSet['add'];

		// Add custom controller redirect to 外送管理
		$buttonSet['route']['handler'] = function()
		{
			$html = <<<HTML
<button class="btn btn-info" onclick="Joomla.submitbutton('routes.redirect')">
	<span class="glyphicon glyphicon-random"></span> 回到外送管理
</button>
HTML;
			$bar = JToolbar::getInstance('toolbar');
			$bar->appendButton('Custom', $html);
		};

		$buttonSet['add']['access'] = false;

		return $buttonSet;
	}
}
