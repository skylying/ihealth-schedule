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
 * Class RxindividualsHtmlView
 *
 * @since 1.0
 */
class ScheduleViewRxindividualHtml extends EditView
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
	protected $name = 'rxindividual';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = 'rxindividual';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = 'rxindividuals';

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
	 * prepareRender
	 *
	 * @return  void
	 */
	protected function prepareRender()
	{
		parent::prepareRender();

		$data = $this->getData();

		$images = \Schedule\Helper\ImageHelper::getImages($data->item->id, 'rxindividual');

		$data->images = $images;

		$this->setData($data);
	}

	/**
	 * configToolbar
	 *
	 * @param  array $buttonSet
	 * @param  bool  $canDo
	 *
	 * @return array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		// Get default button set.
		$buttonSet = parent::configureToolbar($buttonSet, $canDo);

		// Add custom controller redirect to print overview layout
		$buttonSet['print']['handler'] = function ()
		{
			$html = <<<HTML
<button class="btn btn-info" onclick="jQuery('input[name=save-and-print]').val('1');Joomla.submitbutton('rxindividual.edit.apply')">
	<span class="glyphicon glyphicon-print"></span> 儲存&列印
</button>
HTML;
			$bar  = JToolbar::getInstance('toolbar');

			$bar->appendButton('custom', $html);
		};

		return $buttonSet;
	}

	/**
	 * prepareData
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
		$data       = $this->data;
		$customerId = $data->item->customer_id;
		$itemId     = $data->item->id;

		// Check item id and customer id
		if (!empty($itemId) && !empty($customerId))
		{
			$this->preparePrintData();
		}
	}

	/**
	 * preparePrintData
	 *
	 * @return  void
	 */
	public function preparePrintData()
	{
		$app = JFactory::getApplication();

		$isSaveAndPrint = $app->getUserState('save-and-print');

		// SaveAndPrint state has only used once
		$app->setUserState('save-and-print', null);

		$data = $this->data;

		// Age calculation
		$birthday = new DateTime($data->item->birth_date);
		$age      = $birthday->diff(new Datetime('now'))->y;

		$memberList    = '';

		// Get print data from helpers
		$customerInfo = \Schedule\Helper\RxDataHelper::getCustomerInfo($data->item->customer_id);
		$members       = \Schedule\Helper\Mapping\MemberCustomerHelper::loadMembers($data->item->id);
		$scheduleInfos = \Schedule\Helper\RxDataHelper::getRxList($data->item->id);
		$drugs         = \Schedule\Helper\RxDataHelper::getDrugList($data->item->id);

		foreach ($members as $member)
		{
			$memberList .= $member->name . ' ';
		}

		$data->item->scheduleInfos = $scheduleInfos;
		$data->item->member_list   = $memberList;
		$data->item->customer_note = $customerInfo->note;
		$data->item->age           = $age;
		$data->item->drugs         = $drugs;
		$data->item->count         = count($drugs);
		$data->item->need_split    = $customerInfo->need_split;
		$data->print               = $isSaveAndPrint;
	}
}
