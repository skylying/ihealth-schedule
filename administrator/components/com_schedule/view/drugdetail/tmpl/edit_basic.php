<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Schedule\Helper\DataSortHelper;
use Schedule\Helper\Mapping\MemberCustomerHelper;
use Schedule\Helper\Form\FieldHelper;

$container = $this->getContainer();
$asset     = $container->get('helper.asset');
$form      = $data->form;

JHtmlJquery::framework(true);

$asset->addJS('multi-row-handler.js');

$addSenderTitleId = DataSortHelper::getBeforeColumnChangeIndex($data->items, "sender");
$addExtraBoxId = DataSortHelper::getBeforeColumnChangeIndex($data->items, "institute_id");
?>

<h3 class="text-right">
	<?php echo $data->date; ?>
</h3>

<h3>
	<?php echo $data->items[0]->sender_name; ?>
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
		<?php foreach ($data->items as $item): ?>
			<?php if (in_array($item->id, $addSenderTitleId)): ?>
					</tbody>
				</table>

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
			<?php endif; ?>
			<tr>
				<td>
					<!-- 排程編號 -->
					<?php echo $item->id; ?>
				</td>
				<td>
					<!-- 處方編號 -->
					<?php echo $item->rx_id; ?>
				</td>
				<td>
					<!-- 處方建立時間 -->
					<?php echo $item->created; ?>
				</td>
				<td>
					<!-- 吃完藥日 -->
					<?php echo $item->drug_empty_date; ?>
				</td>
				<td>
					<!-- 所屬機構/會員 -->
					<?php
					if ("resident" == $item->type)
					{
						echo $item->institute_title;
					}
					elseif ("individual" == $item->type)
					{
						$members = MemberCustomerHelper::loadMembers($item->customer_id);

						foreach ($members as $member)
						{
							echo $member->name;

							if (end($members) != $member)
							{
								echo "<br />";
							}
						}
					}
					?>
				</td>
				<td>
					<!-- 縣市 -->
					<?php echo $item->city_title; ?>
				</td>
				<td>
					<!-- 區域 -->
					<?php echo $item->area_title; ?>
				</td>
				<td>
					<!-- 客戶 -->
					<?php echo $item->customer_name; ?>
				</td>
				<td>
					<!-- 分要完成 form -->
					<?php
					$sorted = FieldHelper::resetGroup($form->getField('sorted'), "schedule.{$item->id}");

					echo $sorted->input;
					?>
				</td>
				<td>
					<!-- 冰品 -->
					<?php
					$ice = FieldHelper::resetGroup($form->getField('ice'), "schedule.{$item->id}");

					echo $ice->input;
					?>
				</td>
				<td>
					<!-- 自費金額 -->
					<?php
					$price = FieldHelper::resetGroup($form->getField('price'), "schedule.{$item->id}");

					echo $price->input;
					?>
				</td>
				<td>
					<!-- 最後編輯者 -->
					<!-- TODO: 我們 schedule 沒有修改欄位 -->
				</td>
			</tr>

			<?php if (in_array($item->id, $addExtraBoxId)): ?>
				<tr>
					<td colspan="11" class="text-right"><!-- TODO: 份數 --> 份</td>
					<td>
						<!-- TODO: js -->
						<button id="button-institute<?php echo $item->institute_id; ?>" type="button">+</button>
					</td>
				</tr>
			<?php endif; ?>

		<?php endforeach; ?>
	</tbody>
</table>

<script id="row-template" class="hide" type="text/html">
	<?php echo $this->loadTemplate('list_row', array('group' => 'items.0hash0', 'form' => $data->form)); ?>
</script>
