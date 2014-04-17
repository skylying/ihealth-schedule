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
use Windwalker\Xul\XulEngine;

// No direct access
defined('_JEXEC') or die;

/**
 * Class SchedulesHtmlView
 *
 * @since 1.0
 */
class ScheduleViewScheduleHtml extends EditView
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
	protected $name = 'schedule';

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
		$this->engine = new PhpEngine;

		parent::__construct($model, $container, $config, $paths);
	}

	protected function prepareRender()
	{
		parent::prepareRender();

		$data = $this->getData();

		$data->formInstitute = $this->get('FormInstitute');
		$data->formIndividual = $this->get('FormIndividual');
	}

	/**
	 * setTitle
	 *
	 * @param   string  $title
	 * @param   string  $icons
	 *
	 * @return  void
	 */
	protected function setTitle($title = null, $icons = 'pencil-2')
	{
		if (!$title)
		{
			$key = 'COM_%s_%s_TITLE_ITEM_' . ($this->data->item->id ? 'EDIT' : 'NEW');

			$title = \JText::_(sprintf($key, strtoupper($this->prefix), strtoupper($this->viewItem)));
		}

		parent::setTitle($title, 'pencil-2 article');
	}

	/**
	 * configureToolbar
	 *
	 * @param array  $buttonSet
	 * @param Object $canDo
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		$config = parent::configureToolbar($buttonSet, $canDo);

		$config['apply']['access'] = false;

		return $config;
	}
}
