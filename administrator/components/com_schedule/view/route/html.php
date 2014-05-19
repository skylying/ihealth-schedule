<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\DI\Container;
use Windwalker\Model\Model;
use Windwalker\View\Engine\PhpEngine;
use Windwalker\View\Html\EditView;

// No direct access
defined('_JEXEC') or die;

/**
 * Class RoutesHtmlView
 *
 * @since 1.0
 */
class ScheduleViewRouteHtml extends EditView
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
	protected $name = 'route';

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
	 * @param Model             $model      The model object.
	 * @param Container         $container  DI Container.
	 * @param array             $config     View config.
	 * @param SplPriorityQueue  $paths      Paths queue.
	 */
	public function __construct(Model $model = null, Container $container = null, $config = array(), \SplPriorityQueue $paths = null)
	{
		$this->engine = new PhpEngine;

		parent::__construct($model, $container, $config, $paths);
	}

	/**
	 * prepareData
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
		/** @var JInput $input */
		$input = $this->getContainer()->get('input');
		$data  = $this->getData();

		$id  = $input->get('id');
		$cid = $input->get('cid', array(), 'ARRAY');

		if (! empty($id))
		{
			$cid[] = $id;

			$cid = array_unique($cid);
		}

		$data->cid = $cid;
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
}
