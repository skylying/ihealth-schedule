<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
$scheduleInfos = $data->item->scheduleInfos;
$drugs = $data->item->drugs;
$params = $data->item->params;
$params['fromOfficialSite'] = JArrayHelper::getValue($params, 'fromOfficialSite', '');
?>

<style>
	tr td:nth-child(2)
	{
		border-right: 1px solid #efefef;
	}

	.from-official-site
	{
		padding: 10px;
	}
	@media print {
		.printButton
		{
			display: none;
		}
		.pagebreak
		{
			page-break-before: always;
		}
		@page
		{
  			size: auto;	/* auto is the initial value */
			margin: 0mm;  /* this affects the margin in the printer settings */
		}
	}
	.boldfont
	{
		font-weight: bold; font-size: 16px;
	}
</style>
<script>
function markPrinted()
{
	var adminForm = window.opener.document.forms["adminForm"];
	adminForm.elements["jform[printed]"].value = 1;
	window.opener.Joomla.submitbutton('rxindividual.edit.apply');
	window.close();
}
</script>

<div class="row-fluid">
	<div class="col-lg-12 center printButton" style="padding-top: 10px;">
		<a class="btn btn-default btn-info" onclick="window.print();markPrinted();">
			<i class="glyphicon glyphicon-print">
				列印
			</i>
		</a>
		<a class="btn btn-default btn-danger" onclick="window.close();">
			<i class="glyphicon glyphicon-remove-circle">
				關閉視窗
			</i>
		</a>
	</div>
</div>
<?php foreach ($scheduleInfos as $scheduleInfo): ?>
	<div class="content">
		<div class="row-fluid">
			<div class="col-lg-12">
				<h2 class="center">
					<?php if ($params['fromOfficialSite'] == 'true'): ?>
					<label class="label label-warning from-official-site"><span class="glyphicon glyphicon-user">官網客戶</span></label>
					<?php endif; ?>
					<?php echo $data->item->customer_name; ?>
					第
					<?php echo substr($scheduleInfo->deliver_nth, 0, 1); ?>
					次的外送資料
					(處方箋編號: <?php echo $data->item->id; ?>)
				</h2>
				<?php if ($data->item->method == 'form')
				{
					echo '<div class="alert alert-info center" role="alert">本處方箋需確認正本</div>';
				}
				?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="col-lg-12 left">
				<table class="table table-striped" style="border: 1px solid #C0BCBC;">
					<thead>
					<tr>
						<th class="left" style="width: 20%;">項次</th>
						<th class="left" style="width: 30%; border-right: 1px solid #efefef;">內容</th>
						<th class="left" style="width: 20%;">項次</th>
						<th class="left" style="width: 30%;">內容</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>所屬會員</td>
						<td><?php echo $data->item->member_name; ?></td>
						<td>客戶身分證字號</td>
						<td><?php echo $data->item->id_number; ?></td>
					</tr>
					<tr>
						<td class="boldfont">處方姓名</td>
						<td class="boldfont"><?php echo $data->item->customer_name; ?></td>
						<td>客戶生日</td>
						<td><?php echo $data->item->birth_date; ?></td>
					</tr>
					<tr>
						<td class="boldfont">客戶備註</td>
						<td class="boldfont"><?php echo nl2br($data->item->customer_note); ?></td>
						<td>處方開立醫院</td>
						<td><?php echo $data->item->hospital_title; ?></td>
					</tr>
					<tr>
						<td class="boldfont">本次外送備註</td>
						<td class="boldfont"><?php echo nl2br($data->item->note); ?></td>
						<td><!--科別--></td>
						<td><!--科別--></td>
					</tr>
					<tr>
						<td class="boldfont">地址</td>
						<td class="boldfont"><?php echo $scheduleInfo->city_title . ' - ' . $scheduleInfo->area_title . ' - ' . $scheduleInfo->address; ?></td>
						<td>就醫日期</td>
						<td><?php echo $data->item->see_dr_date; ?></td>
					</tr>
					<tr>
						<td class="boldfont">電話(H)</td>
						<td class="boldfont"><?php echo $scheduleInfo->tel_home; ?></td>
						<td>給藥天數</td>
						<td><?php echo $data->item->period; ?>天</td>
					</tr>
					<tr>
						<td class="boldfont">電話(O)</td>
						<td class="boldfont"><?php echo $scheduleInfo->tel_office; ?></td>
						<td>可調劑次數</td>
						<td><?php echo $data->item->times; ?>次</td>
					</tr>
					<tr>
						<td class="boldfont">手機(M)</td>
						<td class="boldfont"><?php echo $scheduleInfo->mobile; ?></td>
						<td><!--是否分包--></td>
						<td><?php // echo JText::_('COM_SCHEDULE_RXINDIVIDUAL_PRINT_NEEDSPLIT_' . $data->item->need_split);?></td>
					</tr>
					<tr>
						<td class="boldfont">預約宅配日</td>
						<td class="boldfont"><?php echo $scheduleInfo->date; ?></td>
						<td><!--外送次數--></td>
						<td><?php // echo $data->item->times; ?></td>
					</tr>
					<tr>
						<td class="boldfont">時段</td>
						<td class="boldfont"><?php echo JText::_('COM_SCHEDULE_RXINDIVIDUAL_PRINT_' . $scheduleInfo->session); ?></td>
						<td><!--客戶年齡--></td>
						<td><?php // echo $data->item->age; ?></td>
					</tr>
					<tr>
						<td>處方箋狀態</td>
						<td><?php echo $data->item->received ? '取得' : '未取得'; ?></td>
						<td><!--藥品吃完日--></td>
						<td><?php // echo $scheduleInfo->drug_empty_date; ?></td>
					</tr>
					<tr>
						<td>處方箋上傳方式</td>
						<td><?php echo JText::_('COM_SCHEDULE_RXINDIVIDUAL_PRINT_' . $data->item->method); ?></td>
						<td><!--宅配次數--></td>
						<td><!--第 <?php // echo substr($scheduleInfo->deliver_nth, 0, 1); ?> 次--></td>
					</tr>
					<?php if ($data->item->method == 'form'): ?>
					<tr >
						<td></td>
						<td></td>
						<td>藥品資料</td>
						<td>
							<table class="table table-bordered">
								<thead>
								<tr>
									<th>藥品健保碼</th>
									<th>顆數量</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($drugs as $drug):?>
									<tr>
										<td>
											<?php echo $drug->hicode; ?>
										</td>
										<td>
											<?php echo $drug->quantity; ?>
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td></td>
						<td></td>
						<td>藥品種數</td>
						<td><?php echo $data->item->count; ?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="pagebreak"></div>
<?php endforeach; ?>

