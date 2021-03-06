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
 * Class InstitutesHtmlView
 *
 * @since 1.0
 */
class ScheduleViewInstitutesHtml extends GridView
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
	protected $name = 'institutes';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'institute';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'institutes';

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
			'view_name' => 'institute',
			'view_item' => 'institute',
			'view_list' => 'institutes',

			// Column which we allow to drag sort
			'order_column'   => 'institute.id',

			// Table id
			'order_table_id' => 'instituteList',

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

		$buttonSet['sync'] = array(
			'handler' => function ()
			{
				$html = <<<HTML
<button class="btn btn-small" type="button"
	onclick="if(confirm('同步資料約需要 5~10 分鐘，同步期間請勿關閉視窗，確定要同步嗎?')){Joomla.submitbutton('institutes.sync');}">
	<span class="glyphicon glyphicon-transfer"></span> 同步
</button>
HTML;
				$bar = JToolbar::getInstance('toolbar');

				$bar->appendButton('custom', $html);
			},
		);

		// In debug mode, we remove trash button but use delete button instead.
		$buttonSet['trash']['access'] = false;
		$buttonSet['delete']['access'] = true;

		$buttonSet['publish']['access'] = false;
		$buttonSet['unpublish']['access'] = false;
		$buttonSet['edit']['access'] = false;
		$buttonSet['checkin']['access'] = false;
		$buttonSet['add']['access'] = false;
		$buttonSet['duplicate']['access'] = false;
		$buttonSet['delete']['access'] = false;

		return $buttonSet;
	}
}
