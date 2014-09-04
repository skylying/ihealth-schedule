<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Table;
use Schedule\Helper\ImageHelper;
use Schedule\Table\Collection as TableCollection;
use Schedule\Helper\ScheduleHelper;
use Schedule\Helper\MailHelper;
use Windwalker\Model\Exception\ValidateFailException;

/**
 * Class SaveController
 *
 * @since 1.0
 */
class ScheduleControllerRxindividualEditSave extends SaveController
{
	/**
	 * Use DB transaction or not.
	 *
	 * @var  boolean
	 */
	protected $useTransaction = true;

	/**
	 * Mappers
	 *
	 * @var  DataMapper[]
	 */
	protected $mapper = array();

	/**
	 * Property addressModel.
	 *
	 * @var  ScheduleModelAddress
	 */
	protected $addressModel;

	/**
	 * Property customer.
	 *
	 * @var  Data
	 */
	protected $customer;

	/**
	 * Property isNew.
	 *
	 * @var  bool
	 */
	protected $isNew = true;

	/**
	 * Prepare Execute
	 *
	 * @return  void
	 */
	protected function prepareExecute()
	{
		$this->initMapper();

		parent::prepareExecute();
	}

	/**
	 * 初始化 Mapper
	 *
	 * @return  void
	 */
	protected function initMapper()
	{
		if (empty($this->mapper))
		{
			$this->mapper['customer'] = new DataMapper(Table::CUSTOMERS);
			$this->mapper['customer'] = new DataMapper(Table::CUSTOMERS);
			$this->mapper['address']  = new DataMapper(Table::ADDRESSES);
			$this->mapper['sender']   = new DataMapper(Table::SENDERS);
			$this->mapper['drug']     = new DataMapper(Table::DRUGS);
			$this->mapper['task']     = new DataMapper(Table::TASKS);
			$this->mapper['routes']   = new DataMapper(Table::ROUTES);
			$this->mapper['schedule'] = new DataMapper(Table::SCHEDULES);
		}
	}

	/**
	 * preSaveHook
	 *
	 * @return  void
	 */
	protected function preSaveHook()
	{
		$nthList = $this->getSelectedNthList();

		$this->validateSchedules($nthList);

		$this->addressModel = $this->getModel("Address");
		$this->customer = $this->getUpdatedCustomerData($this->data['customer_id']);
		$this->isNew = empty($this->data['id']) || $this->data['id'] <= 0;

		$this->data["deliver_nths"] = implode(",", $nthList);
		$this->data["empty_date_1st"] = $this->data["schedules_1st"]["drug_empty_date"];
		$this->data["empty_date_2nd"] = $this->data["schedules_2nd"]["drug_empty_date"];
		$this->data["remind"] = isset($this->data['remind']) ? implode(",", $this->data['remind']) : "";

		$this->createAddress();

		parent::preSaveHook();
	}

	/**
	 * postSaveHook
	 *
	 * @param   \Windwalker\Model\CrudModel $model
	 * @param   array                       $validData
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validData)
	{
		// Update customer data
		$this->getModel("Customer")->save((array) $this->customer);

		$this->data['id'] = $model->getState()->get("rxindividual.id");

		/** @var ScheduleModelSchedule $scheduleModel */
		$scheduleModel = $this->getModel("Schedule");
		$scheduleState = $scheduleModel->getState();

		$scheduleState->set('form.type', 'schedule_individual');

		// 藥品健保碼處理
		$this->processDrug();

		// 最後一次更改的地址
		$lastAddress = null;

		// Get a simple array of schedule data
		$schedules = array();

		foreach (array("1st", "2nd", "3rd") as $nth)
		{
			$schedule = $this->data["schedules_{$nth}"];

			// 跳過沒有需要外送的次數
			if (empty($schedule["deliver_nth"]) || ! isset($schedule["deliver_nth"]))
			{
				$this->deleteSchedule($schedule['schedule_id']);

				if (! empty($schedule['schedule_id']))
				{
					$this->data["schedules_{$nth}"]['send_confirm_email'] = true;
				}

				continue;
			}

			$scheduleTable = TableCollection::loadTable('Schedule', $schedule['schedule_id']);
			$address       = $this->mapper['address']->findOne($schedule["address_id"]);
			$lastAddress   = $address;
			$route         = $this->getUpdatedRouteData($address, $schedule);
			$sender        = $this->mapper['sender']->findOne($route->sender_id);
			$task          = $this->getUpdatedScheduleTaskData($sender, $schedule);

			$this->data["schedules_{$nth}"] = $this->getScheduleUploadData($task->id, $address, $nth, $schedule, $route);

			$scheduleModel->getState()->set("sender_id", $route->sender_id);
			$scheduleModel->save($this->data["schedules_{$nth}"]);

			$checkedSchedule = ScheduleHelper::checkScheduleChanged($scheduleTable->getProperties(), $this->data["schedules_{$nth}"]);

			// If schedule was updated or new a schedule then we will send this email with data.
			if ((! empty($scheduleTable->id) && $checkedSchedule !== false) || empty($scheduleTable->id))
			{
				$this->data["schedules_{$nth}"]['send_confirm_email'] = true;

				$scheduleId = $scheduleModel->getState()->get('schedule.id');

				$schedules[] = $scheduleModel->getItem($scheduleId);
			}
		}

		// Flush default address when lastAddress is not empty
		if (! empty($lastAddress))
		{
			$this->addressModel->flushDefaultAddress($this->customer->id, $lastAddress->id);
		}

		/** @var ScheduleModelCustomer $customerModel */
		$customerModel = $this->getModel('Customer', '', array('ignore_request' => true));

		$customerModel->setCustomerState(1, [$this->customer->id]);

		// Send notify email to member
		if ($this->sendNotifyMailToMember())
		{
			$memberTable = TableCollection::loadTable('Member', $validData['member_id']);
			$drugsModel = $this->getModel('Drugs');

			$drugsModel->getState()->set('filter', array('drug.rx_id' => $this->data['id']));

			$mailData = array(
				'schedules' => $schedules,
				'rx'        => new Data($model->getItem($this->data['id'])),
				'drugs'     => $drugsModel->getItems(),
				'member'    => $memberTable,
			);

			MailHelper::sendMailWhenScheduleChange($memberTable->email, $mailData);
		}

		// Store images
		$imageModel = $this->getModel('Image');

		foreach (['ajax_image1', 'ajax_image2', 'ajax_image3'] as $key)
		{
			if ($this->data[$key] > 0)
			{
				$image = array(
					'id' => $this->data[$key],
					'rx_id' => $this->data['id'],
				);

				$imageModel->save($image);
			}
		}
	}

	/**
	 * sendNotifyMailToMember
	 *
	 * @return  bool
	 */
	private function sendNotifyMailToMember()
	{
		if ($this->isNew)
		{
			return true;
		}

		foreach (array("1st", "2nd", "3rd") as $nth)
		{
			if (! empty($this->data["schedules_{$nth}"]['send_confirm_email']))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Drug 處理
	 *
	 * @return  stdClass
	 */
	protected function processDrug()
	{
		$drugModel = $this->getModel("Drug");

		// 健保 json
		$drugs = isset($this->data['drug']) ? json_decode($this->data['drug']) : array();
		$deleteDrugIds = isset($this->data['delete_drug']) ? json_decode($this->data['delete_drug']) : array();

		$this->data['drugs'] = array();

		// 新增健保碼
		if (! empty($drugs))
		{
			foreach ($drugs as $drug)
			{
				$drug->rx_id = $this->data['id'];

				$drug = (array) $drug;

				$this->data['drugs'][] = $drug;

				$drugModel->save($drug);
			}
		}

		// 刪除健保碼
		if (! empty($deleteDrugIds))
		{
			$this->mapper['drug']->delete(array("id" => $deleteDrugIds));
		}

		return $drugModel;
	}

	/**
	 * Delete Schedule
	 *
	 * @param   integer $id
	 *
	 * @return  void
	 */
	protected function deleteSchedule($id = null)
	{
		if (empty($id))
		{
			return;
		}

		$this->mapper['schedule']->delete(array('id' => $id));
	}

	/**
	 * 取得更新後的 Route 資料
	 *
	 * @param   stdClass $address
	 * @param   array    $schedule
	 *
	 * @return  Data
	 *
	 * @throws  Exception
	 */
	protected function getUpdatedRouteData($address, $schedule)
	{
		$routeModel = $this->getModel("Route");

		// 外送路線
		$route = $this->mapper['routes']->findOne(array("city" => $address->city, "area" => $address->area, "type" => "customer"));

		// 沒有路線的時候新增路線
		if ($route->isNull())
		{
			// 用設定的 id 取出 sender
			$sender = $this->mapper['sender']->findOne($schedule['sender_id']);

			// 沒取到 sender
			if ($sender->isNull())
			{
				throw new \Exception("error sender id");
			}

			// 整理存入資料
			$routeData = array(
				"city"      => $address->city,
				"area"      => $address->area,
				"sender_id" => $sender->id,
				"weekday"   => $schedule['weekday'],
				"type"      => "customer"
			);

			$routeModel->save($routeData);

			// 補上 id
			$routeData['id'] = $routeModel->getState()->get("route.id");

			// 更新 route 變數給下面使用
			$route = new Data($routeData);
		}

		return $route;
	}

	/**
	 * 取得更新後的 task 資料
	 *
	 * @param   object $sender
	 * @param   array  $schedule
	 *
	 * @return  Data
	 */
	protected function getUpdatedScheduleTaskData($sender, $schedule)
	{
		$taskModel  = $this->getModel("Task");

		// 取得同日期同藥師的外送紀錄
		$task = $this->mapper['task']->findOne(array("sender" => $sender->id, "date" => $schedule['date']));

		// 如果有外送資料就直接使用
		if (! $task->isNull())
		{
			return $task;
		}

		// 準備新增外送資料
		$task->date        = $schedule['date'];
		$task->sender      = $sender->id;
		$task->sender_name = $sender->name;
		$task->status      = 0;

		// 新增外送
		$taskModel->save((array) $task);

		// 塞回 id
		$task->id = $taskModel->getState()->get("task.id");

		return $task;
	}

	/**
	 * 取得更新後的 Customer 資料
	 *
	 * @param   integer $id
	 *
	 * @return  Data
	 *
	 * @throws  \Exception
	 */
	protected function getUpdatedCustomerData($id)
	{
		$customer = $this->mapper['customer']->findOne($id);

		if ($customer->isNull())
		{
			throw new \Exception("error customer id");
		}

		$customer->hospital   = JArrayHelper::getValue($this->data, 'hospital_id', 0);
		$customer->tel_office = JArrayHelper::getValue($this->data, 'tel_office', '');
		$customer->tel_home   = JArrayHelper::getValue($this->data, 'tel_home', '');
		$customer->mobile     = JArrayHelper::getValue($this->data, 'mobile', '');

		return $customer;
	}

	/**
	 * 取得 Schedule 更新的資料
	 *
	 * @param   integer $task
	 * @param   Data    $address
	 * @param   string  $nth
	 * @param   array   $formData
	 * @param   Data    $route
	 *
	 * @return  array
	 */
	protected function getScheduleUploadData($task, $address, $nth, $formData, $route)
	{
		$scheduleUploadData = array(
			"id"          => $formData['schedule_id'],
			"rx_id"       => $this->data['id'],
			"route_id"    => $route->id,
			"member_id"   => $this->data['member_id'],
			"task_id"     => $task,
			"type"        => "individual",
			"customer_id" => $this->customer->id,
			"address_id"  => $address->id,
			"deliver_nth" => $nth,
			"tel_office"  => isset($formData['tel_office']) ? $formData['tel_office'] : "",
			"tel_home"    => isset($formData['tel_home']) ? $formData['tel_home'] : "",
			"mobile"      => isset($formData['mobile']) ? $formData['mobile'] : "",
			"status"      => "scheduled",
			"sorted"      => 0
		);

		return array_merge($formData, $scheduleUploadData);
	}

	/**
	 * Create Address
	 *
	 * @return  void
	 */
	protected function createAddress()
	{
		$createAddresses = isset($this->data['create_addresses']) ? json_decode($this->data['create_addresses']) : array();

		if (! empty($createAddresses))
		{
			$addressIdMap = array();

			// 新增地址資料
			foreach ($createAddresses as $address)
			{
				$this->addressModel->save(
					array(
						"customer_id" => $this->customer->id,
						"city"        => $address->city,
						"area"        => $address->area,
						"address"     => $address->address
					)
				);

				// Hash id map
				$addressIdMap[$address->id] = $this->addressModel->getState()->get("address.id");
			}

			// 做更新 hash id 成 實際 id 動作
			foreach (array("1st", "2nd", "3rd") as $val)
			{
				$schedule = $this->data["schedules_{$val}"];

				$hashId = $schedule["address_id"];

				// 如果是有記錄過的 hash id 把 hash id 更新成實際 id
				if (isset($addressIdMap[$hashId]))
				{
					$this->data["schedules_{$val}"]["address_id"] = $addressIdMap[$hashId];
				}
			}
		}
	}

	/**
	 * 取得有被勾選的 nth 清單
	 *
	 * @return  array
	 */
	protected function getSelectedNthList()
	{
		$nthList = array();

		foreach (array("1st", "2nd", "3rd") as $nth)
		{
			if (! empty($this->data["schedules_{$nth}"]["deliver_nth"]))
			{
				$nthList[] = $nth;
			}
		}

		return $nthList;
	}

	/**
	 * validateSchedules
	 *
	 * @param array $nthList
	 *
	 * @return  void
	 *
	 * @throws \Windwalker\Model\Exception\ValidateFailException
	 */
	protected function validateSchedules(array $nthList)
	{
		/** @var ScheduleModelRxIndividual $model */
		$model = $this->getModel();
		$form = $model->getSchedulesForm();
		$validWeekdays = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
		$errors = [];

		foreach (array('1st', '2nd', '3rd') as $nth)
		{
			if (! in_array($nth, $nthList))
			{
				$form->removeGroup("schedules_{$nth}");

				continue;
			}

			$schedule = $this->data["schedules_{$nth}"];

			// Check tel_office, tel_home, and mobile
			if (empty($schedule['tel_office'])
				&& empty($schedule['tel_home'])
				&& empty($schedule['mobile']))
			{
				$errors[] = JText::_('COM_SCHEDULE_SCHEDULE_' . $nth) . '排程請輸入至少一個連絡方式';
			}

			// Check sender information
			if (!is_numeric($schedule['address_id']))
			{
				if ((int) $schedule['sender_id'] <= 0)
				{
					$errors[] = JText::_('COM_SCHEDULE_SCHEDULE_' . $nth) . '排程請選擇配送藥師';
				}

				if (!in_array($schedule['weekday'], $validWeekdays))
				{
					$errors[] = JText::_('COM_SCHEDULE_SCHEDULE_' . $nth) . '排程請選擇配送日';
				}
			}
		}

		if (count($errors) > 0)
		{
			throw new ValidateFailException($errors);
		}

		$model->validate($form, $this->data);
	}
}
