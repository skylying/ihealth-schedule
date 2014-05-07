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
use Windwalker\Helper\ArrayHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class RxresidentsHtmlView
 *
 * @since 1.0
 */
class ScheduleViewRxresidentHtml extends EditView
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
	protected $name = 'rxresident';

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
		$items = $this->getItems();

		/** @var ScheduleModelRxresident $model */
		$model = $this->getModel();

		$data = $this->getData();
		$data->forms = array();
		$data->instituteForm = $model->getForm(array('id' => -1));
		$data->templateForm = $model->getForm(array('id' => 0));

		foreach ($items as $item)
		{
			$data->instituteForm->bind($item);

			break;
		}

		foreach ($items as $hash => $item)
		{
			$data->forms[$hash] = $model->getForm(array('id' => $hash));

			$data->forms[$hash]->bind($item);
		}
	}

	/**
	 * Get items from url query input or session
	 *
	 * @return  array
	 */
	protected function getItems()
	{
		$items = array();
		$app = JFactory::getApplication();
		$key = $this->option . '.item.edit.save.data';
		$data = $app->getUserState($key);

		if ($data)
		{
			foreach (ArrayHelper::getValue($data, 'items', array()) as $hash => $item)
			{
				$items[$hash] = $item;
			}

			$app->setUserState($key, null);
		}
		else
		{
			/** @var ScheduleModelRxresident $model */
			$model = $this->getModel();
			/** @var JInput $input */
			$input = $this->container->get('input');

			foreach ($input->get('cid', array(), 'ARRAY') as $id)
			{
				$items[$id] = (array) $model->getItem($id);
			}
		}

		return $items;
	}

	/**
	 * configureToolbar
	 *
	 * @param array   $buttonSet
	 * @param object  $canDo
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		// Get default button set.
		$buttonSet = parent::configureToolbar($buttonSet, $canDo);

		$buttonSet['apply']['access'] = false;
		$buttonSet['save2copy']['access'] = false;

		return $buttonSet;
	}
}
