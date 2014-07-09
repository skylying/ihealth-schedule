<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Helper\Mapping\MemberCustomerHelper;
use Schedule\Helper\Form\FieldHelper;
use Windwalker\Joomla\DataMapper\DataMapper;

/**
 * Prepare data for this template.
 *
 * @var $data Windwalker\Data\Data
 * @var $form JForm
 */
$form     = $data->form;
$schedule = $data->schedule;

$sorted = FieldHelper::resetGroup($form->getField('sorted', null, $schedule->sorted), "schedules.{$schedule->id}");
$ice    = FieldHelper::resetGroup($form->getField('ice', null, $schedule->ice), "schedules.{$schedule->id}");
$price  = FieldHelper::resetGroup($form->getField('price', null, (int) $schedule->price), "schedules.{$schedule->id}");
// @ 最後編輯者是否要每一筆獨立更新需再和 iHealth 討論
//$modified_by = FieldHelper::resetGroup($form->getField('modified_by', null, 'hello'), "schedules.{$schedule->id}");
?>
<tr>
	<td>
		<!-- 排程編號 -->
		<?php echo $schedule->id; ?>
	</td>
	<td>
		<!-- 處方編號 -->
		<?php echo $schedule->rx_id; ?>
	</td>
	<td>
		<!-- 處方建立時間 -->
		<?php echo substr($schedule->created, 0, 10); ?>
	</td>
	<td>
		<!-- 吃完藥日 -->
		<?php echo $schedule->drug_empty_date; ?>
	</td>
	<!-- 所屬機構/會員 -->
	<?php
	switch ($schedule->type)
	{
		case ("resident"):
			echo "<td class='alert alert-info'>" . $schedule->institute_title . "</td>";
		break;

		case ("individual"):
			echo "<td class='alert alert-warning'>" . $schedule->member_name . "</td>";
		break;
	}
	?>
	<td>
		<!-- 縣市 -->
		<?php echo $schedule->city_title; ?>
	</td>
	<td>
		<!-- 區域 -->
		<?php echo $schedule->area_title; ?>
	</td>
	<td>
		<!-- 客戶 -->
		<?php echo $schedule->customer_name; ?>
	</td>
	<td>
		<!-- 分藥完成 form -->
		<?php echo $sorted->input; ?>
	</td>
	<td>
		<!-- 冰品 -->
		<?php echo $ice->input; ?>
	</td>
	<td>
		<!-- 自費金額 -->
		<?php echo $price->input; ?>
	</td>
	<td>
		<!-- 最後編輯者 @ 最後編輯者是否要每一筆獨立更新需再和 iHealth 討論-->

		<?php
/*		if (!empty($schedule->modified_by))
		{
			$userMapper = new DataMapper('#__users');

			$modifier = $userMapper->findOne(['id' => $schedule->modified_by]);

			echo $modifier->name;
		}

		echo $modified_by->input;
		*/?>
	</td>
</tr>
