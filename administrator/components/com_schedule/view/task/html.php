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

			$data->item->schedules = (new DataMapper(Table::SCHEDULES))->find(['task_id' => $data->item->id]);
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
}
