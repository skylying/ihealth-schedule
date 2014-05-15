<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Table;

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
	 * preSaveHook
	 *
	 * @return  void
	 */
	protected function preSaveHook()
	{
		$customerMapper = new DataMapper(Table::CUSTOMERS);
		$cityMapper     = new DataMapper(Table::CITIES);
		$areaMapper     = new DataMapper(Table::AREAS);

		$addressModel  = $this->getModel("Address");

		$createAddress = isset($this->data['create_address']) ? json_decode($this->data['create_address']) : array();

		$customer = $customerMapper->findOne($this->data['customer_id']);

		if (! empty($createAddress))
		{
			$hashId = array();

			// 新增地址資料
			foreach ($createAddress as $addressTmp)
			{
				$city = $cityMapper->findOne($addressTmp->city);
				$area = $areaMapper->findOne($addressTmp->area);

				$addressModel->save(
					array(
						"customer_id" => $customer->id,
						"city"        => $city->id,
						"city_title"  => $city->title,
						"area"        => $area->id,
						"area_title"  => $area->title,
						"address"     => $addressTmp->address
					)
				);

				$address = $addressModel->getItem();

				// Hash id map
				$hashId[$addressTmp->id] = $address->id;
			}

			// 塞回資料
			foreach (array("1st", "2nd", "3rd") as $val)
			{
				$schedule = $this->data["schedules_{$val}"];

				$addressId = $schedule["address_id"];

				// 如果 address id 在 hash map 有記錄 更新 id
				if (isset($hashId[$addressId]))
				{
					// 塞回資料
					$this->data["schedules_{$val}"]["address_id"] = $hashId[$addressId];
				}
			}
		}

		// 處方客人資料
		$this->data["customer_name"] = $customer->name;
		$this->data["type"]          = $customer->type;

		// 外送次數
		$nths = array();

		foreach (array("1st", "2nd", "3rd") as $val)
		{
			// 沒有值跳過
			if (empty($this->data["schedules_{$val}"]["deliver_nth"]))
			{
				continue;
			}

			$nths[] = $val;
		}

		// 組好他有勾選的值
		$nths = implode(",", $nths);

		// 塞入資料
		$this->data["deliver_nths"] = $nths;

		parent::preSaveHook();
	}

	/**
	 * postSaveHook
	 *
	 * @param \Windwalker\Model\CrudModel $model
	 * @param array                       $validData
	 *
	 * @return void
	 */
	protected function postSaveHook($model, $validData)
	{
		$rx = $model->getItem();

		// Mappers
		$addressMapper = new DataMapper(Table::ADDRESSES);
		$senderMapper  = new DataMapper(Table::SENDERS);

		// Get model
		$scheduleModel = $this->getModel("Schedule");
		$addressModel  = $this->getModel("Address");

		// 圖片處理
		$this->rxImageHandler();

		// 客戶處理
		$customer = $this->getCustomer($this->data['customer_id']);

		// 健保處理
		$this->drugHandler();

		// 最後更改地址
		$lastAddress = null;

		// 新增排程
		foreach (array("1st", "2nd", "3rd") as $nth)
		{
			// 使用者上傳的排程資料
			$schedule = $this->data["schedules_{$nth}"];

			// 沒有需要外送的次數跳過
			if (empty($schedule["deliver_nth"]) || ! isset($schedule["deliver_nth"]))
			{
				$this->deleteSchedule($schedule['schedule_id']);

				continue;
			}

			// 外送地址比對
			$address = $addressMapper->findOne($schedule["address_id"]);

			// 外送路線
			$routes = $this->getRoute($address, $schedule);

			// 外送者
			$sender = $senderMapper->findOne($routes->sender_id);

			// Get task
			$taskId = $this->getScheduleTask($sender, $schedule);

			// 新增排程
			$scheduleModel->save(
				$this->getScheduleUploadData($rx, $taskId, $customer, $address, $nth, $schedule)
			);

			// 最後更改地址
			$lastAddress = $address;
		}

		// 如果有新增排程
		if (empty($lastAddress))
		{
			// Flush Default Address
			$addressModel->flushDefaultAddress($customer->id, $lastAddress->id);
		}
	}

	/**
	 * Drug 處理
	 *
	 * @return  stdClass
	 */
	protected function drugHandler()
	{
		$rx = $this->model->getItem();
		$drugModel = $this->getModel("Drug");

		// 健保 json
		$drugs = isset($this->data['drug']) ? json_decode($this->data['drug']) : array();

		// 新增健保碼
		if (! empty($drugs))
		{
			foreach ($drugs as $drug)
			{
				$drug->rx_id = $rx->id;

				$drugModel->save((array) $drug);
			}
		}

		return $drugModel;
	}

	/**
	 * Get Schedule
	 *
	 * @param integer $id
	 *
	 * @return  \Windwalker\Data\Data
	 */
	protected function getSchedule($id = null)
	{
		if (empty($id))
		{
			return new \Windwalker\Data\Data;
		}

		$scheduleMapper = new DataMapper(Table::SCHEDULES);

		return $scheduleMapper->findOne($id);
	}

	/**
	 * Delete Schedule
	 *
	 * @param integer $id
	 *
	 * @return  boolean
	 */
	protected function deleteSchedule($id = null)
	{
		if (empty($id))
		{
			return true;
		}

		$scheduleMapper = new DataMapper(Table::SCHEDULES);

		$scheduleMapper->delete(array('id' => $id));

		return true;
	}

	/**
	 * Get Route 如果沒有對應 route 新增
	 *
	 * @param stdClass $address
	 * @param array    $option
	 *
	 * @return \Windwalker\Data\Data
	 *
	 * @throws Exception
	 */
	protected function getRoute($address, $option)
	{
		$routeModel   = $this->getModel("Route");
		$routesMapper = new DataMapper(Table::ROUTES);
		$senderMapper = new DataMapper(Table::SENDERS);

		// 外送路線
		$route = $routesMapper->findOne(array("city" => $address->city, "area" => $address->area, "type" => "institute"));

		// 沒有路線的時候新增路線
		if ($route->isNull())
		{
			// 用設定的 id 取出 sender
			$sender = $senderMapper->findOne($option['sender_id']);

			// 沒取到 sender
			if ($sender->isNull())
			{
				throw new \Exception("error sender id");
			}

			// 整理存入資料
			$routeData = array(
				"city"        => $address->city,
				"area"        => $address->area,
				"city_title"  => $address->city_title,
				"area_title"  => $address->area_title,
				"sender_id"   => $sender->id,
				"sender_name" => $sender->name,
				"weekday"     => $option['weekday'],
				"type"        => "institute"
			);

			$routeModel->save($routeData);

			$routeId = $routeModel->getItem()->id;

			// 更新 route 變數給下面使用
			$route = $routesMapper->findOne($routeId);
		}

		return $route;
	}

	/**
	 * 取得 Task 沒有對應 task 時 新增
	 *
	 * @param object $sender
	 * @param array  $option
	 *
	 * @return  integer
	 */
	protected function getScheduleTask($sender, $option)
	{
		$taskModel  = $this->getModel("Task");
		$taskMapper = new DataMapper(Table::TASKS);

		// 同日期同藥師取得 外送
		$task = $taskMapper->findOne(array("sender" => $sender->id, "date" => $option['date']));

		// 如果有取得對應 外送
		if ($task->isNull())
		{
			return $task->id;
		}

		// 沒有外送時 新增
		$taskData = array(
			"date"        => $option['date'],
			"sender"      => $sender->id,
			"sender_name" => $sender->name,
			"status"      => 0
		);

		// 新增外送
		$taskModel->save($taskData);

		return $taskModel->getState()->get("task.id");
	}

	/**
	 * 取得 Customer
	 *
	 * @param integer $id
	 *
	 * @return \Windwalker\Data\Data
	 *
	 * @throws \Exception
	 */
	protected function getCustomer($id)
	{
		$customerMapper = new DataMapper(Table::CUSTOMERS);

		$customer = $customerMapper->findOne($id);

		// 找不到客戶
		if ($customer->isNull())
		{
			throw new \Exception("error customer id");
		}

		$customerModel = $this->getModel("Customer");

		$customerUpdata = array(
			"id"         => $id,
			"tel_office" => $this->data['tel_office'],
			"tel_home"   => $this->data['tel_home'],
			"mobile"     => $this->data['mobile']
		);

		// 更新客戶電話
		$customerModel->save($customerUpdata);

		return new Data($customerModel->getItem());
	}

	/**
	 * 取得 Schedule 更新的資料
	 *
	 * @param \Windwalker\Data\Data $rx
	 * @param integer               $taskId
	 * @param \Windwalker\Data\Data $customer
	 * @param \Windwalker\Data\Data $address
	 * @param \Windwalker\Data\Data $nth
	 * @param \Windwalker\Data\Data $formData
	 *
	 * @return  array
	 */
	protected function getScheduleUploadData($rx, $taskId, $customer, $address, $nth, $formData)
	{
		// Schedule data
		$scheduleUpdata = array(
			// Id
			"id"            => $formData['schedule_id'],

			// Rx id
			"rx_id"         => $rx->id,

			// 對應外送 id
			"task_id"       => $taskId,
			"type"          => $customer->type,
			"customer_id"   => $customer->id,
			"customer_name" => $customer->name,

			"address_id"    => $address->id,
			"city"          => $address->city,
			"city_title"    => $address->city_title,
			"area"          => $address->area,
			"area_title"    => $address->area_title,
			"address"       => $address->address,

			// 第幾次宅配
			"deliver_nth"   => $nth,

			// Default
			"status"        => "scheduled",
			"sorted"        => 0
		);

		return $scheduleUpdata;
	}

	/**
	 * 圖片資料處理
	 *
	 * @return void
	 */
	protected function rxImageHandler()
	{
		$rx = $this->model->getItem();

		$files = $this->input->files->getVar('jform');

		// 圖片上傳
		\Schedule\Helper\ImageHelper::handleUpload($rx->id, $files['rximages']);

		$removeCid = isset($this->data['remove_images']) ? $this->data['remove_images'] : array();

		// 刪除圖片
		\Schedule\Helper\ImageHelper::removeImages($removeCid);
	}
}
