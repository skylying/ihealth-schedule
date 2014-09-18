<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Helper\DateHelper;
use Joomla\DI\Container;
use Windwalker\Model\Model;
use Windwalker\View\Engine\PhpEngine;
use Windwalker\View\Html\GridView;
use Windwalker\Xul\XulEngine;

// No direct access
defined('_JEXEC') or die;

/**
 * Class HolidaysHtmlView
 *
 * @since 1.0
 */
class ScheduleViewHolidaysHtml extends GridView
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
	protected $name = 'holidays';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'holiday';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'holidays';

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
			'view_name' => 'holiday',
			'view_item' => 'holiday',
			'view_list' => 'holidays',

			// Column which we allow to drag sort
			'order_column'   => 'holiday.catid, holiday.ordering',

			// Table id
			'order_table_id' => 'holidayList',

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
		$year = DateHelper::getDate()->format('Y', true);

		// Get current year from filter value
		$this->data->currentYear = $this->data->filterForm->getValue('holiday.year', 'filter', $year);

		// Extract information we need
		$dates = array_map(
			function ($item)
			{
				return array(
					'id'      => $item->id,
					'year'    => $item->year,
					'month'   => $item->month,
					'day'     => $item->day,
					'title'   => $item->title,
					'weekday' => $item->weekday,
					'date'    => $item->date,
					'state'   => $item->state
				);
			},
			$this->data->items
		);

		$offDays = array();

		/**
		 * Sort all holidays data by same month, output will look like :
		 * Array
		 * (
		 *     [4] => Array
		 *     (
		 *         [26] => stdClass Object
		 *         (
		 *             [id] => 1
		 *             [title] => 週末
		 *             [weekday] => MON
		 *         )
		 *         [27] => stdClass Object
		 *         (
		 *             [id] => 2
		 *             [title] => 週末
		 *             [weekday] => TUE
		 *         )
		 *     )
		 * )
		 */
		foreach ($dates as $date)
		{
			$month = $date['month'];
			$day   = $date['day'];

			$offDays[$month][$day] = (object) array(
				'id'      => $date['id'],
				'title'   => $date['title'],
				'weekday' => $date['weekday'],
				'date'    => $date['date'],
				'state'   => $date['state']
			);
		}

		// Export to global
		$this->data->offDays = $offDays;
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

		// Remove all buttons we do not need
		$buttonSet['edit']['access'] = false;
		$buttonSet['duplicate']['access'] = false;
		$buttonSet['publish']['access'] = false;
		$buttonSet['unpublish']['access'] = false;
		$buttonSet['checkin']['access'] = false;
		$buttonSet['delete']['access'] = false;
		$buttonSet['trash']['access'] = false;
		$buttonSet['batch']['access'] = false;
		$buttonSet['preferences']['access'] = false;

		$buttonSet['add']['args'] = array($this->viewItem . '.edit.save', 'title' => '儲存變更');

		return $buttonSet;
	}
}
