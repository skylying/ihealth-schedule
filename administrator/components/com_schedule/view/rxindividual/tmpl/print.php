<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
	<div class="col-lg-12 center">
		<a class="btn btn-default btn-info" onclick="window.print();">
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
<div class="row-fluid">
	<div class="col-lg-12">
		<h2 class="center">
			這是
			<?php echo $data->item->customer_name;?>
			第
			<?php echo $data->item->deliver_nths;?>
			次的外送資料
			(處方箋編號:
			<?php echo $data->item->id;?>
			)
		</h2>
	</div>
</div>
<div class="row-fluid">
	<div class="col-lg-12 center">
		<table class="table table-striped">
			<thead>
			<th class="center">項次</th>
			<th class="center">內容</th>
			<th class="center">項次</th>
			<th class="center">內容</th>
			</thead>
			<tbody>
			<tr>
				<td>所屬會員</td>
				<td><?php echo $data->item->member_name;?></td>
				<td>宅配次數</td>
				<td></td>
			</tr>
			<tr>
				<td>處方姓名</td>
				<td><?php echo $data->item->customer_name;?></td>
				<td>藥品吃完日</td>
				<td></td>
			</tr>
			<tr>
				<td>客戶身分證字號</td>
				<td><?php echo $data->item->id_number;?></td>
				<td>預約宅配日</td>
				<td></td>
			</tr>
			<tr>
				<td>客戶生日</td>
				<td><?php echo $data->item->birth_date;?></td>
				<td>時段</td>
				<td></td>
			</tr>
			<tr>
				<td>客戶年齡</td>
				<td></td>
				<td>時段</td>
				<td></td>
			</tr>
			<tr>
				<td>處方箋狀態</td>
				<td><?php echo $data->item->received ? '取得' : '未取得';?></td>
				<td>地址</td>
				<td></td>
			</tr>
			<tr>
				<td>處方箋上傳方式</td>
				<td><?php echo $data->item->method;?></td>
				<td>電話(H)</td>
				<td></td>
			</tr>
			<tr>
				<td>處方開立醫院</td>
				<td><?php echo $data->item->hospital_title;?></td>
				<td>電話(O)</td>
				<td></td>
			</tr>
			<tr>
				<td>就醫日期</td>
				<td><?php echo $data->item->see_dr_date;?></td>
				<td>手機(M)</td>
				<td></td>
			</tr>
			<tr>
				<td>給藥天數</td>
				<td><?php echo $data->item->period;?>天</td>
				<td>本次外送備註</td>
				<td><?php echo $data->item->note;?></td>
			</tr>
			<tr>
				<td>可調劑次數</td>
				<td><?php echo $data->item->times;?>次</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>外送次數</td>
				<td><?php echo $data->item->times;?></td>
				<td>客戶備註</td>
				<td></td>
			</tr>
			<tr>
				<td>藥品資料</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>藥品種數</td>
				<td></td>
				<td>註記選項</td>
				<td><?php echo $data->item->remind;?></td>
			</tr>
			</tbody>
		</table>
	</div>
<div>

