<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Joomla\DataMapper\DataMapper;
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

		$files = $this->input->files->getVar('jform');

		// 圖片上傳
		\Schedule\Helper\ImageHelper::handleUpload($rx->id, $files['rximages']);

		$removeCid = isset($this->data['remove_images']) ? $this->data['remove_images'] : array();

		// 刪除圖片
		\Schedule\Helper\ImageHelper::removeImages($removeCid);

		// Mappers
		$customerMapper = new DataMapper(Table::CUSTOMERS);
		$addressMapper  = new DataMapper(Table::ADDRESSES);
		$routesMapper   = new DataMapper(Table::ROUTES);
		$senderMapper   = new DataMapper(Table::SENDERS);
		$scheduleMapper = new DataMapper(Table::SCHEDULES);
		$taskMapper     = new DataMapper(Table::TASKS);

		$customer = $customerMapper->findOne($this->data['customer_id']);

		$customer->tel_office = $this->data['tel_office'];
		$customer->tel_home   = $this->data['tel_home'];
		$customer->mobile     = $this->data['mobile'];

		// 更新客戶電話
		$customerMapper->updateOne($customer);

		// Get model
		$taskModel     = $this->getModel("Task");
		$scheduleModel = $this->getModel("Schedule");
		$addressModel  = $this->getModel("Address");
		$routeModel    = $this->getModel("Route");
		$drugModel     = $this->getModel("Drug");

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

		// 新增排程次數
		$scheduleDoTimes = 0;

		// 新增排程
		foreach (array("1st", "2nd", "3rd") as $val)
		{
			// 使用者上傳的排程資料
			$schedule = $this->data["schedules_{$val}"];

			// 現在這筆排程的資料
			if (empty($schedule['schedule_id']))
			{
				$thisScheduleData = new \Windwalker\Data\Data;
			}
			else
			{
				$thisScheduleData = $scheduleMapper->findOne($schedule['schedule_id']);
			}

			// 沒有需要外送的次數跳過
			if (empty($schedule["deliver_nth"]) || ! isset($schedule["deliver_nth"]))
			{
				if (! empty($schedule['schedule_id']))
				{
					$scheduleMapper->delete(['id' => $schedule['schedule_id']]);
				}

				continue;
			}

			// 外送地址比對
			$address = $addressMapper->findOne($schedule["address_id"]);

			// 外送路線
			$routes = $routesMapper->findOne(array("city" => $address->city, "area" => $address->area));

			// 沒有路線的時候新增路線
			if (! isset($routes->id))
			{
				// 用設定的 id 取出 sender
				$sender = $senderMapper->findOne($schedule['sender_id']);

				// 沒取到 sender
				if (! isset($sender->id))
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
					"weekday"     => $schedule['weekday']
				);

				$routeModel->save($routeData);

				$routeId = $routeModel->getItem()->id;

				// 更新 route 變數給下面使用
				$routes = $routesMapper->findOne($routeId);
			}

			// 外送者
			$sender = $senderMapper->findOne($routes->sender_id);

			// Get task
			$task = $taskMapper->findOne(array("sender" => $sender->id, "sender_name" => $sender->name));

			// 如果沒取得 task , 是新增
			if (empty($task->id))
			{
				// Task data
				$taskData = array(
					"id" => $thisScheduleData->task_id,
					"sender" => $sender->id,
					"sender_name" => $sender->name,
					"status" => 0
				);

				// 新增外送
				$taskModel->save($taskData);

				// 取出剛剛新增的外送管理
				$task = $taskModel->getItem();
			}

			// 對應處方箋 id
			$thisScheduleData->rx_id = $rx->id;

			// Schedule data
			$scheduleUpdata = array(
				// Rx id
				"rx_id"           => $rx->id,

				// 對應外送 id
				"task_id"         => $task->id,
				"type"            => $customer->type,
				"customer_id"     => $customer->id,
				"customer_name"   => $customer->name,

				"address_id"      => $address->id,
				"city"            => $address->city,
				"city_title"      => $address->city_title,
				"area"            => $address->area,
				"area_title"      => $address->area_title,
				"address"         => $address->address,

				// 第幾次宅配
				"deliver_nth"     => $val,

				// Default
				"status"          => "scheduled",
				"sorted"          => 0
			);

			// 新增排程
			$scheduleModel->save(array_merge((array) $thisScheduleData, $schedule, $scheduleUpdata));

			// 記錄次數
			$scheduleDoTimes++;
		}

		// 如果有新增排程
		if (0 < $scheduleDoTimes)
		{
			// Flush Default Address
			$addressModel->flushDefaultAddress($customer->id, $address->id);
		}
	}
}
