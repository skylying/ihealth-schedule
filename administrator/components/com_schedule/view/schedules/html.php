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
			$buttonSet['trash']['access']  = false;
			$buttonSet['delete']['access'] = true;
		}

		$buttonSet['add']['args'] = array_merge($buttonSet['add']['args'], array('新增行政排程'));

		$buttonSet['publish']['access'] = false;
		$buttonSet['edit']['access'] = false;
		$buttonSet['unpublish']['access'] = false;
		$buttonSet['checkin']['access'] = false;
		$buttonSet['batch']['access'] = false;

		// Add a print popup button
		$buttonSet['print'] = array(
			'handler' => function ()
			{
				JFactory::getDocument()->addStyleDeclaration('
					#modal-print {
  						overflow-y: hidden;
					}
					#modal-print iframe {
						border: 0;
					}

					/* fix float problem */
					#toolbar-popup-print .btn-group {
						float: none;
					}
				');

				$printUrl = 'index.php?option=com_schedule&view=schedules&layout=print&tmpl=component';

				// See JToolbarButtonPopup::fetchButton()
				JToolbar::getInstance('toolbar')->appendButton('Popup', 'print', '列印排程統計表', $printUrl);
			},
		);

		return $buttonSet;
	}
}
