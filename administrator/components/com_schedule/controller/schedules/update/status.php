<?php

use Windwalker\Controller\State\AbstractUpdateStateController;

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
	 * Prepare execute hook.
	 *
	 * @throws \LogicException
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		$status      = strtolower(trim($this->input->get('status', '')));
		$validStatus = ['scheduled', 'emergency', 'cancel_reject', 'cancel_only', 'canceled', 'pause'];
		$checkCancel = true;

		if (!in_array($status, $validStatus))
		{
			throw new \InvalidArgumentException('無此狀態 "' . $status . '"');
		}

		$this->stateData  = array('status' => $status);
		$this->actionText = 'STATUS_' . $status;

		if (in_array($status, ['cancel_reject', 'cancel_only']))
		{
			$validCancel = ['badservice', 'changedrug', 'passaway', 'other'];
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
				throw new \InvalidArgumentException('沒有選擇取消的理由');
			}

			// Update "cancel" and "cancel_note" fields
			$this->stateData['cancel']      = $cancel;
			$this->stateData['cancel_note'] = trim($this->input->get('cancel_note', '', 'RAW'));
		}
		else
		{
			// Reset "cancel" and "cancel_note" fields
			$this->stateData['cancel']      = null;
			$this->stateData['cancel_note'] = '';
		}
	}
}
