<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Table;
use Schedule\Helper\ImageHelper;

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
	 * @var  array
	 */
	protected $mapper = array();

	/**
	 * Property rxId.
	 *
	 * @var  integer
	 */
	protected $rxId = 0;

	/**
	 * Instantiate the controller.
	 *
	 * @param \JInput          $input
	 * @param \JApplicationCms $app
	 * @param array            $config
	 */
	public function __construct(\JInput $input = null, \JApplicationCms $app = null, $config = array())
	{
		$this->initMapper();

		parent::__construct($input, $app, $config);
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
			$this->mapper['hospital'] = new DataMapper(Table::HOSPITALS);
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
		$remind = isset($this->data['remind']) ? $this->data['remind'] : array();

		$this->createAddress();

		$this->buildNthOfScheduleToRxData();

		$customer = $this->mapper['customer']->findOne($this->data['customer_id']);
		$hospital = $this->mapper['hospital']->findOne($this->data['hospital_id']);

		// 處方客人資料
		$this->data["customer_name"] = $customer->name;
		$this->data["type"]          = $customer->type;

		// 醫院資料
		$this->data["hospital_title"] = $hospital->title;

		// Rx 吃完藥日
		$this->data["empty_date_1st"] = $this->data["schedules_1st"]["drug_empty_date"];
		$this->data["empty_date_2nd"] = $this->data["schedules_2nd"]["drug_empty_date"];

		// Remind
		$this->data["remind"] = implode(",", $remind);

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
		$this->rxId = $model->getState()->get("rxindividual.id");

		// Get model
		$scheduleModel = $this->getModel("Schedule");
		$addressModel  = $this->getModel("Address");

		$scheduleState = $scheduleModel->getState();
		$scheduleState->set('form.type', 'schedule_individule');

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
			$address = $this->mapper['address']->findOne($schedule["address_id"]);

			// 外送路線
			$routes = $this->getRoute($address, $schedule);

			// 外送者
			$sender = $this->mapper['address']->findOne($routes->sender_id);

			// Get task
			$task = $this->getScheduleTask($sender, $schedule);

			// 新增排程
			$scheduleModel->save(
				$this->getScheduleUploadData($this->rxId, $task->id, $customer, $address, $nth, $schedule, $routes)
			);

			// 最後更改地址
			$lastAddress = $address;
		}

		// 如果有最後地址
		if (! empty($lastAddress))
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
		$drugModel = $this->getModel("Drug");

		// 健保 json
		$drugs = isset($this->data['drug']) ? json_decode($this->data['drug']) : array();
		$deleteDrugIds = isset($this->data['delete_drug']) ? json_decode($this->data['delete_drug']) : array();

		// 新增健保碼
		if (! empty($drugs))
		{
			foreach ($drugs as $drug)
			{
				$drug->rx_id = $this->rxId;

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
	 * Get Route 如果沒有對應 route 新增
	 *
	 * @param   stdClass $address
	 * @param   array    $schedule
	 *
	 * @return  Data
	 *
	 * @throws  Exception
	 */
	protected function getRoute($address, $schedule)
	{
		$routeModel   = $this->getModel("Route");

		// 外送路線
		$route = $this->mapper['routes']->findOne(array("city" => $address->city, "area" => $address->area, "type" => "institute"));

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
				"city"        => $address->city,
				"area"        => $address->area,
				"sender_id"   => $sender->id,
				"sender_name" => $sender->name,
				"weekday"     => $schedule['weekday'],
				"type"        => "customer"
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
	 * 取得 Task 沒有對應 task 時 新增
	 *
	 * @param   object $sender
	 * @param   array  $schedule
	 *
	 * @return  Data
	 */
	protected function getScheduleTask($sender, $schedule)
	{
		$taskModel  = $this->getModel("Task");

		// 同日期同藥師取得 外送
		$task = $this->mapper['task']->findOne(array("sender" => $sender->id, "date" => $schedule['date']));

		// 如果有取得對應 外送
		if (! $task->isNull())
		{
			return $task;
		}

		// 沒有外送時 新增
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
	 * 取得 Customer
	 *
	 * @param   integer $id
	 *
	 * @return  Data
	 *
	 * @throws  \Exception
	 */
	protected function getCustomer($id)
	{
		$customer = $this->mapper['customer']->findOne($id);

		// 找不到客戶
		if ($customer->isNull())
		{
			throw new \Exception("error customer id");
		}

		$customerModel = $this->getModel("Customer");

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
	 * @param   integer $rxId
	 * @param   integer $task
	 * @param   Data    $customer
	 * @param   Data    $address
	 * @param   string  $nth
	 * @param   array   $formData
	 * @param   Data    $routes
	 *
	 * @return  array
	 */
	protected function getScheduleUploadData($rxId, $task, $customer, $address, $nth, $formData, $routes)
	{
		// Schedule data
		$scheduleUpdata = array(
			// Id
			"id"            => $formData['schedule_id'],

			// Rx id
			"rx_id"         => $rxId,
			"route_id"      => $routes->id,
			"sender_name"   => $routes->sender_name,

			// 對應外送 id
			"task_id"       => $task,
			"type"          => $customer->type,
			"customer_id"   => $customer->id,
			"customer_name" => $customer->name,

			"address_id"    => $address->id,
			"address"       => $address->address,

			// 第幾次宅配
			"deliver_nth"   => $nth,

			// Default
			"status"        => "scheduled",
			"sorted"        => 0
		);

		return array_merge($formData, $scheduleUpdata);
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

		ImageHelper::resetImagesRxId($resetId, $this->rxId);
	}

	/**
	 * Create Address
	 *
	 * @return  void
	 */
	protected function createAddress()
	{
		$addressModel  = $this->getModel("Address");

		$createAddress = isset($this->data['create_address']) ? json_decode($this->data['create_address']) : array();

		if (! empty($createAddress))
		{
			$hashId = array();

			// 新增地址資料
			foreach ($createAddress as $addressTmp)
			{
				$addressModel->save(
					array(
						"customer_id" => $customer->id,
						"city"        => $addressTmp->id,
						"area"        => $addressTmp->id,
						"address"     => $addressTmp->address
					)
				);

				// Hash id map
				$hashId[$addressTmp->id] = $addressModel->getState()->get("address.id");;
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
	}
}
