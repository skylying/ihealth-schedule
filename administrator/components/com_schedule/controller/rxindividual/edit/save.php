<?php

use Windwalker\Controller\Edit\SaveController,
	Windwalker\Joomla\DataMapper\DataMapper;

/**
 * Class SaveController
 *
 * @since 1.0
 */
class ScheduleControllerRxindividualEditSave extends SaveController
{
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

		$customer = with(new DataMapper("#__schedule_customers"))
			->findOne(array("id" => $this->data['customer_id']));

		// 新增排成
		foreach (array("1st", "2nd", "3rd") as $key)
		{
			// 沒有需要外送的次數跳過
			if (! isset($this->data["schedules_{$key}"]["deliver_nths"]))
			{
				continue;
			}

			// 外送地址比對
			$address = with(new DataMapper("#__schedule_addresses"))
				->findOne(array("id" => $this->data["schedules_{$key}"]["address"]));

			// 外送路線
			$routes = with(new DataMapper("#__schedule_routes"))
				->findOne(array("city" => $address->city, "area" => $address->area));

			// 外送者
			$sender = with(new DataMapper("#__schedule_senders"))
				->findOne(array("id" => $routes->sender_id));

			$task = $this->getModel("task");

			// 新增外送
			$task->save(
				array(
					"sender" => $sender->id,
					"sender_name" => $sender->name,
					"status" => 0
				)
			);

			// 取出剛剛新增的外送管理
			$task = $task->getItem();

			$schedule = $this->getModel("schedule");

			// 新增排成
			$schedule->save(
				array(
					// 對應處方箋 id
					"rx_id"           => $rx->id,

					// 對應外送 id
					"task_id"         => $task->id,
					"type"            => $customer->type,
					"customer_id"     => $customer->id,
					"customer_name"   => $customer->name,
					"city"            => $address->city,
					"city_title"      => $address->city_title,
					"area"            => $address->area,
					"area_title"      => $address->area_title,
					"address"         => $address->address,

					// 第幾次宅配
					"deliver_nth"     => $key,

					// 排程日期
					"date"            => $this->data["schedules_{$key}"]["send_date"],

					// 藥品吃完日
					"drug_empty_date" => $this->data["schedules_{$key}"]["empty_date"],

					// 時段
					"session"         => $this->data["schedules_{$key}"]["send_time"],

					"status"          => "scheduled",
					"sorted"          => 0
				)
			);
		}
	}
}
