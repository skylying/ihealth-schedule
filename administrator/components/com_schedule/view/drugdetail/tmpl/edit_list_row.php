<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Helper\Mapping\MemberCustomerHelper;
use Schedule\Helper\Form\FieldHelper;

/**
 * Prepare data for this template.
 *
 * @var $data Windwalker\Data\Data
 * @var $form JForm
 */
$form     = $data->form;
$schedule = $data->schedule;

$sorted = FieldHelper::resetGroup($form->getField('sorted', null, $schedule->sorted), "schedule.{$schedule->id}");
$ice    = FieldHelper::resetGroup($form->getField('ice', null, $schedule->ice), "schedule.{$schedule->id}");
$price  = FieldHelper::resetGroup($form->getField('price', null, $schedule->price), "schedule.{$schedule->id}");
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
		<?php echo $schedule->created; ?>
	</td>
	<td>
		<!-- 吃完藥日 -->
		<?php echo $schedule->drug_empty_date; ?>
	</td>
	<td>
		<!-- 所屬機構/會員 -->
		<?php
		switch ($schedule->type)
		{
			case ("resident"):
				echo $schedule->institute_title;
			break;

			case ("individual"):
				$members = MemberCustomerHelper::loadMembers($schedule->customer_id);

				$memberNames = \JArrayHelper::getColumn($members, "name");

				echo implode("<br/>", $memberNames);
			break;
		}
		?>
	</td>
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
		<!-- 最後編輯者 -->
		<!-- TODO: 我們 schedule 需要新增這欄位 -->
	</td>
</tr>
