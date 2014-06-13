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
 * Class RxresidentsHtmlView
 *
 * @since 1.0
 */
class ScheduleViewRxresidentsHtml extends GridView
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
	protected $name = 'rxresidents';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'rxresident';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'rxresidents';

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
			'option'         => 'com_schedule',
			'view_name'      => 'rxresident',
			'view_item'      => 'rxresident',
			'view_list'      => 'rxresidents',

			// Column which we allow to drag sort
			'order_column'   => 'rxresident.catid, rxresident.ordering',

			// Table id
			'order_table_id' => 'rxresidentList',

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
	 * configureToolbar
	 *
	 * @param array  $buttonSet
	 * @param object $canDo
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		// Get default button set.
		$buttonSet = parent::configureToolbar($buttonSet, $canDo);

		$buttonSet['edit']['access']      = false;
		$buttonSet['duplicate']['access'] = false;
		$buttonSet['publish']['access']   = false;
		$buttonSet['unpublish']['access'] = false;
		$buttonSet['checkin']['access']   = false;
		$buttonSet['trash']['access']     = false;
		$buttonSet['batch']['access']     = false;
		$buttonSet['delete']['access']    = true;

		return $buttonSet;
	}
}
