<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Table;
use Schedule\Helper\ImageHelper;
use Schedule\Table\Collection as TableCollection;
use Schedule\Helper\ScheduleHelper;
use Schedule\Helper\MailHelper;

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
	 * Property scheduleModel.
	 *
	 * @var  ScheduleModelSchedule
	 */
	protected $scheduleModel;

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
		$this->addressModel = $this->getModel("Address");

		$this->customer = $this->getUpdatedCustomerData($this->data['customer_id']);

		$this->createAddress();

		$this->buildNthOfScheduleToRxData();

		// Rx 吃完藥日
		$this->data["empty_date_1st"] = $this->data["schedules_1st"]["drug_empty_date"];
		$this->data["empty_date_2nd"] = $this->data["schedules_2nd"]["drug_empty_date"];

		// Remind
		$this->data["remind"] = isset($this->data['remind']) ? implode(",", $this->data['remind']) : "";

		$this->isNew = empty($this->data['id']) || $this->data['id'] <= 0;

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
		$this->data['id'] = $model->getState()->get("rxindividual.id");

		$this->scheduleModel = $this->getModel("Schedule");

		$scheduleState = $this->scheduleModel->getState();
		$scheduleState->set('form.type', 'schedule_individule');

		// 圖片處理
		$this->rxImageHandler();

		// 健保處理
		$this->processDrug();

		// 最後更改地址
		$lastAddress = null;

		// Get a simple array of schedule data
		$schedules = array();

		// 新增排程
		foreach (array("1st", "2nd", "3rd") as $nth)
		{
			// 使用者上傳的排程資料
			$schedule = $this->data["schedules_{$nth}"];
			$scheduleTable = TableCollection::loadTable('Schedule', $schedule['schedule_id']);

			// 沒有需要外送的次數跳過
			if (empty($schedule["deliver_nth"]) || ! isset($schedule["deliver_nth"]))
			{
				$this->deleteSchedule($schedule['schedule_id']);

				if (! empty($schedule['schedule_id']))
				{
					$this->data["schedules_{$nth}"]['send_confirm_email'] = true;
				}

				continue;
			}

			// 外送地址比對
			$address = $this->mapper['address']->findOne($schedule["address_id"]);

			// 外送路線
			$route = $this->getUpdatedRouteData($address, $schedule);

			// 外送者
			$sender = $this->mapper['sender']->findOne($route->sender_id);

			// Get task
			$task = $this->getUpdatedScheduleTaskData($sender, $schedule);

			// Schedule sender id
			$this->scheduleModel->getState()->set("sender_id", $route->sender_id);

			$this->data["schedules_{$nth}"] = $this->getScheduleUploadData($task->id, $address, $nth, $schedule, $route);

			// 新增排程
			$this->scheduleModel->save($this->data["schedules_{$nth}"]);

			// 最後更改地址
			$lastAddress = $address;

			if (! empty($scheduleTable->id)
				&& ScheduleHelper::checkScheduleChanged($scheduleTable->getProperties(), $this->data["schedules_{$nth}"]))
			{
				$this->data["schedules_{$nth}"]['send_confirm_email'] = true;
			}

			$schedules[] = $this->data["schedules_{$nth}"];
		}

		// 如果有最後地址
		if (! empty($lastAddress))
		{
			// Flush Default Address
			$this->addressModel->flushDefaultAddress($this->customer->id, $lastAddress->id);
		}

		/** @var ScheduleModelCustomer $customerModel */
		$customerModel = $this->getModel('Customer', '', array('ignore_request' => true));

		$customerModel->setCustomerState(1, [$this->customer->id]);

		// Send notify email to member
		if ($this->sendNotifyMailToMember())
		{
			$customerTable = TableCollection::loadTable('Customer', $validData['customer_id']);
			$memberTable = TableCollection::loadTable('Member', $validData['member_id']);

			$mailData = array(
				"schedules" => $schedules,
				"rx"        => $validData,
				"member"    => $memberTable,
				"customer"  => $customerTable,
			);

			MailHelper::sendMailWhenScheduleChange($memberTable->email, $mailData);
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

		// 新增健保碼
		if (! empty($drugs))
		{
			foreach ($drugs as $drug)
			{
				$drug->rx_id = $this->data['id'];

				$drugModel->save((array) $drug);
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
		$routeModel   = $this->getModel("Route");

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

		// 找不到客戶
		if ($customer->isNull())
		{
			throw new \Exception("error customer id");
		}

		$customerModel = $this->getModel("Customer");

		$customer->hospital   = $this->data['hospital_id'];
		$customer->tel_office = $this->data['tel_office'];
		$customer->tel_home   = $this->data['tel_home'];
		$customer->mobile     = $this->data['mobile'];

		// 更新客戶電話
		$customerModel->save((array) $customer);

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
		// Schedule data
		$scheduleUploadData = array(
			// Id
			"id"            => $formData['schedule_id'],

			// Rx id
			"rx_id"         => $this->data['id'],
			"route_id"      => $route->id,

			// Member
			"member_id"     => $this->data['member_id'],

			// 對應外送 id
			"task_id"       => $task,
			"type"          => "individual",
			"customer_id"   => $this->customer->id,

			// 地址
			"address_id"    => $address->id,

			// 第幾次宅配
			"deliver_nth"   => $nth,

			// Telephone Info
			"tel_office"   => isset($formData['tel_office']) ? $formData['tel_office'] : "",
			"tel_home"     => isset($formData['tel_home']) ? $formData['tel_home'] : "",
			"mobile"       => isset($formData['mobile']) ? $formData['mobile'] : "",

			// Default
			"status"        => "scheduled",
			"sorted"        => 0
		);

		return array_merge($formData, $scheduleUploadData);
	}

	/**
	 * 圖片資料處理
	 *
	 * @return  void
	 */
	protected function rxImageHandler()
	{
		$resetId = array();

		for ($i = 1; $i <= 3; $i++)
		{
			if (isset($this->data["ajax_image{$i}"]))
			{
				$resetId[] = $this->data["ajax_image{$i}"];
			}
		}

		ImageHelper::resetImagesRxId($resetId, $this->data['id'], 'rxindividual');
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
	 * 把 schedule 有選擇的 nth 組起來給 rx 儲存用
	 *
	 * @return  void
	 */
	protected function buildNthOfScheduleToRxData()
	{
		// 外送次數
		$nths = array();

		foreach (array("1st", "2nd", "3rd") as $val)
		{
			// 有值就給
			if (! empty($this->data["schedules_{$val}"]["deliver_nth"]))
			{
				$nths[] = $val;
			}
		}

		// 把勾選的值存成資料庫形式
		$nths = implode(",", $nths);

		$this->data["deliver_nths"] = $nths;
	}
}
