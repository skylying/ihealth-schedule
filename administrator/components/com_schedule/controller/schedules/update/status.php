<?php

use Windwalker\Controller\State\AbstractUpdateStateController;
use Schedule\Table\Table as Table;
use Schedule\Table\Collection as TableCollection;
use Windwalker\Joomla\DataMapper\DataMapper;
use Schedule\Helper\MailHelper;

/**
 * Class ScheduleControllerSchedulesUpdateStatus
 *
 * @since 1.0
 */
class ScheduleControllerSchedulesUpdateStatus extends AbstractUpdateStateController
{
	/**
	 * The data fields to update.
	 *
	 * @var string
	 */
	protected $stateData = array(
		'status' => 'scheduled',
	);

	/**
	 * Action text for translate.
	 *
	 * @var string
	 */
	protected $actionText = 'STATUS';

	/**
	 * Are we allow return?
	 *
	 * @var  boolean
	 */
	protected $allowReturn = true;

	/**
	 * Use DB transaction or not.
	 *
	 * @var  boolean
	 */
	protected $useTransaction = true;

	/**
	 * Property sendNotifyMail.
	 *
	 * @var  array
	 */
	protected $sendNotifyMail = array();

	/**
	 * Prepare execute hook.
	 *
	 * @throws \LogicException
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		$status      = strtolower(trim($this->input->get('status', '')));
		$validStatus = ['scheduled', 'emergency', 'cancel_reject', 'cancel_only', 'canceled', 'pause', 'delivered'];
		$checkCancel = true;

		if (!in_array($status, $validStatus))
		{
			$this->redirectToList('無此狀態 "' . $status . '"', 'warning');
		}

		$this->stateData  = array('status' => $status);
		$this->actionText = 'STATUS_' . $status;

		if (in_array($status, ['cancel_reject', 'cancel_only']))
		{
			$validCancel = ['badservice', 'changedrug', 'passaway', 'other'];

			foreach ($this->input->get('cid', array(), 'ARRAY') as $id)
			{
				if ($id > 0)
				{
					if (! empty(TableCollection::loadTable('Schedule', $id)->id))
					{
						$this->sendNotifyMail[] = $id;
					}
				}
			}
		}
		elseif ('pause' === $status)
		{
			$validCancel = ['hospitalized', 'other'];
		}
		else
		{
			$validCancel = [];
			$checkCancel = false;
		}

		if ($checkCancel)
		{
			$cancel = strtolower(trim($this->input->get('cancel', '')));

			if (!in_array($cancel, $validCancel))
			{
				$this->redirectToList('沒有選擇取消的理由', 'warning');
			}

			// Update "cancel" and "cancel_note" fields
			$this->stateData['cancel']      = $cancel;
			$this->stateData['cancel_note'] = trim($this->input->get('cancel_note', '', 'RAW'));
		}
	}

	/**
	 * postUpdateHook
	 *
	 * @param \Windwalker\Model\Model $model
	 *
	 * @return  void
	 */
	protected function postUpdateHook($model)
	{
		parent::postUpdateHook($model);

		foreach ($this->sendNotifyMail as $scheduleId)
		{
			$oldScheduleTable = TableCollection::loadTable('Schedule', $scheduleId);

			$memberTable = TableCollection::loadTable('Member', $oldScheduleTable->member_id);
			$rx = (new DataMapper(Table::PRESCRIPTIONS))->findOne($oldScheduleTable->rx_id);

			if (!empty($memberTable->email) && 'individual' === $rx->type)
			{
				$schedules = (new DataMapper(Table::SCHEDULES))->find(array('rx_id' => $oldScheduleTable->rx_id));
				$drugsModel = $this->getModel('Drugs');
				$drugsModel->getState()->set('filter', array('drug.rx_id' => $oldScheduleTable->rx_id));

				$mailData = array(
					'rx'        => $rx,
					'drugs'     => $drugsModel->getItems(),
					'member'    => $memberTable,
				);

				if (in_array($this->stateData['status'], ['cancel_reject', 'cancel_only']))
				{
					$mailData['schedule'] = $oldScheduleTable;

					MailHelper::sendCancelLayout($memberTable->email, $mailData);
				}
				else
				{
					$mailData['schedules'] = $schedules;

					MailHelper::sendMailWhenScheduleChange($memberTable->email, $mailData);
				}
			}
		}
	}
}
