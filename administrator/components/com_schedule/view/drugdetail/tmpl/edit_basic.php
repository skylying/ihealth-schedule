<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Schedule\Helper\Mapping\MemberCustomerHelper;
use Schedule\Helper\Form\FieldHelper;

$container = $this->getContainer();
$asset     = $container->get('helper.asset');
$form      = $data->form;

JHtmlJquery::framework(true);

$asset->addJS('multi-row-handler.js');

$lastInstituteId = null;
$lastScheduleId  = null;

?>

<h3 class="text-right">
	<?php echo $data->date; ?>
</h3>

<?php foreach ($data->items as $item): ?>
	<h3>
		<?php echo $item->sender_name; ?>
	</h3>

	<table id="drug-details" class="table table-bordered">
	<thead>
	<tr>
		<th>
			排程編號
		</th>
		<th>
			處方箋編號
		</th>
		<th>
			新增處方箋日
		</th>
		<th>
			吃完藥日
		</th>
		<th>
			所屬機構/會員
		</th>
		<th>
			縣市
		</th>
		<th>
			區域
		</th>
		<th>
			客戶
		</th>
		<th>
			完成分藥
		</th>
		<th>
			冰品
		</th>
		<th>
			自費金額
		</th>
		<th>
			最後編輯者
		</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($item->schedules as $schedule): ?>


		<?php if ($schedule->institute_id !== $lastInstituteId && ! empty($lastInstituteId)): ?>
			<tr>
				<td colspan="11" class="text-right"><!-- TODO: 份數 --> 份</td>
				<td>
					<!-- TODO: js -->
					<button id="button-institute<?php echo $lastScheduleId; ?>" type="button">+</button>
				</td>
			</tr>
		<?php endif; ?>

		<?php

		$lastInstituteId = $schedule->institute_id;
		$lastScheduleId  = $schedule->id;

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
				switch ($schedule->type):
					case "resident":
						echo $schedule->institute_title;
					break;
					case "individual":
						$members = MemberCustomerHelper::loadMembers($schedule->customer_id);

						foreach ($members as $member)
						{
							echo $member->name;

							if (end($members) != $member)
							{
								echo "<br />";
							}
						}
					break;
				endswitch;
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
				<!-- 分要完成 form -->
				<?php
				$sorted = FieldHelper::resetGroup($form->getField('sorted'), "schedule.{$schedule->id}");

				echo $sorted->input;
				?>
			</td>
			<td>
				<!-- 冰品 -->
				<?php
				$ice = FieldHelper::resetGroup($form->getField('ice'), "schedule.{$schedule->id}");

				echo $ice->input;
				?>
			</td>
			<td>
				<!-- 自費金額 -->
				<?php
				$price = FieldHelper::resetGroup($form->getField('price'), "schedule.{$schedule->id}");

				echo $price->input;
				?>
			</td>
			<td>
				<!-- 最後編輯者 -->
				<!-- TODO: 我們 schedule 沒有修改欄位 -->
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php endforeach; ?>

<script id="row-template" class="hide" type="text/html">
	<?php echo $this->loadTemplate('list_row', array('group' => 'items.0hash0', 'form' => $data->form)); ?>
</script>
