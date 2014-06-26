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
		/** @var ScheduleModelRxresident $model */
		$model = $this->getModel();

		$postData = $this->getPostData();

		$data = $this->getData();

		$data->forms = array();
		$data->instituteForm = $model->getForm(array('id' => -1));
		$data->templateForm = $model->getForm(array('id' => 0));
		$data->institute = $postData['institute'];
		$data->instituteForm->bind($postData['institute']);

		foreach ($postData['items'] as $hash => $item)
		{
			$data->forms[$hash] = $model->getForm(array('id' => $hash));

			$data->forms[$hash]->bind($item);
		}

		// Check if in edit mode
		$id = (int) $this->container->get('input')->get('id');

		$data->isEdit = ($id > 0);
	}

	/**
	 * Get post data from session or url query input
	 *
	 * @return  array
	 */
	protected function getPostData()
	{
		$app = JFactory::getApplication();
		$key = $this->option . '.edit.' . $this->getName() . '.data';
		$data = $app->getUserState($key);
		$institute = [];
		$items = [];

		if ($data)
		{
			$items = ArrayHelper::getValue($data, 'items', []);

			$app->setUserState($key, null);
		}
		else
		{
			$data = [];

			/** @var ScheduleModelRxresident $model */
			$model = $this->getModel();
			/** @var JInput $input */
			$input = $this->container->get('input');

			$id = $input->get('id');

			if (! empty($id))
			{
				$items[$id] = (array) $model->getItem($id);
			}

			if (count($items) > 0)
			{
				$data = current($items);

				$data['institute_id_selection'] = $data['institute_id'] . '-' . $data['floor'];
			}
		}

		$institute['institute_id']           = ArrayHelper::getValue($data, 'institute_id', '');
		$institute['institute_id_selection'] = ArrayHelper::getValue($data, 'institute_id_selection', '');
		$institute['floor']                  = ArrayHelper::getValue($data, 'floor', '');
		$institute['color_hex']              = ArrayHelper::getValue($data, 'color_hex', '#ffffff');
		$institute['delivery_weekday']       = ArrayHelper::getValue($data, 'delivery_weekday', '');

		if (! empty($institute['delivery_weekday']))
		{
			$institute['delivery_weekday'] = JText::_('COM_SCHEDULE_DELIVERY_WEEKDAY_' . $institute['delivery_weekday']);
		}

		return array(
			'institute' => $institute,
			'items' => $items,
		);
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
