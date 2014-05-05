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
	 * preSaveHook
	 *
	 * @return  void
	 */
	protected function preSaveHook()
	{
		$customerMapper = new DataMapper(Table::CUSTOMERS);
		$customer = $customerMapper->findOne($this->data['customer_id']);

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

		// TODO: 上傳圖片功能

		// Mappers
		$customerMapper = new DataMapper(Table::CUSTOMERS);
		$addressMapper  = new DataMapper(Table::ADDRESSES);
		$routesMapper   = new DataMapper(Table::ROUTES);
		$senderMapper   = new DataMapper(Table::SENDERS);
		$scheduleMapper = new DataMapper(Table::SCHEDULES);

		$customer = $customerMapper->findOne($this->data['customer_id']);

		$customer->tel_office = $this->data['tel_office'];
		$customer->tel_home   = $this->data['tel_home'];
		$customer->mobile     = $this->data['mobile'];

		// 更新客戶電話
		$customerMapper->updateOne($customer);

		// Get model
		$taskModel     = $this->getModel("task");
		$scheduleModel = $this->getModel("Schedule");

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

			// 外送者
			$sender = $senderMapper->findOne($routes->sender_id);

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
		}
	}
}
