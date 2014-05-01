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
		$nths = array();

		foreach (array("1st", "2nd", "3rd") as $val)
		{
			// 沒有值跳過
			if (empty($this->data["schedules_{$val}"]["deliver_nths"]))
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
		$customerMapper = new DataMapper(Table::CUSTOMERS);
		$addressMapper  = new DataMapper(Table::ADDRESSES);
		$routesMapper   = new DataMapper(Table::ROUTES);
		$senderMapper   = new DataMapper(Table::SENDERS);

		$customer = $customerMapper->findOne($this->data['customer_id']);

		$customer->tel_office = $this->data['tel_office'];
		$customer->tel_home   = $this->data['tel_home'];
		$customer->mobile     = $this->data['mobile'];

		// 更新客戶電話
		$customerMapper->updateOne($customer);

		// 新增排程
		foreach (array("1st", "2nd", "3rd") as $val)
		{
			// 沒有需要外送的次數跳過
			if (empty($this->data["schedules_{$val}"]["deliver_nths"]))
			{
				continue;
			}

			// 外送地址比對
			$address = $addressMapper->findOne($this->data["schedules_{$val}"]["address"]);

			// 外送路線
			$routes  = $routesMapper->findOne(array("city" => $address->city, "area" => $address->area));

			// 外送者
			$sender  = $senderMapper->findOne($routes->sender_id);

			$task = $this->getModel("task");

			// Task data
			$taskData = array(
				"sender" => $sender->id,
				"sender_name" => $sender->name,
				"status" => 0
			);

			// 新增外送
			$task->save($taskData);

			// 取出剛剛新增的外送管理
			$task = $task->getItem();

			$schedule = $this->getModel("Schedule");

			// Schedule data
			$scheduleData = array(
				// 對應處方箋 id
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

				// 排程日期
				"date"            => $this->data["schedules_{$val}"]["send_date"],

				// 藥品吃完日
				"drug_empty_date" => $this->data["schedules_{$val}"]["empty_date"],

				// 時段
				"session"         => $this->data["schedules_{$val}"]["send_time"],

				"status"          => "scheduled",
				"sorted"          => 0
			);

			// 新增排程
			$schedule->save($scheduleData);
		}
	}
}
