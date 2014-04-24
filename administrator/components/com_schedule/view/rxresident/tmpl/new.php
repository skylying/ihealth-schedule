<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

$container = $this->getContainer();
$form      = $data->form;
$item      = $data->item;
$fieldsets = $data->form->getFieldsets();

$formGlobal = $form->getFieldset("global");
$formItem   = $form->getFieldset("item");
?>

<form id="adminForm" name="adminForm" action="" method="post" class="form-horizontal">
	<div class="row-fluid">
		<div class="span4">
			<?php echo $formGlobal['jform_institute']->getControlGroup(); ?>
		</div>
		<div class="span4 offset4">
			<?php echo $formGlobal['jform_floor']->getControlGroup(); ?>
		</div>
	</div>

	<table class="table">
		<thead>
		<tr>
			<th style="width:  3%;">編號</th>
			<th style="width:  8%;">住民</th>
			<th style="width:  8%;">身分證字號</th>
			<th style="width: 10%;">生日</th>
			<th style="width: 10%;">就醫日期</th>
			<th style="width:  5%;">處方箋天數</th>
			<th style="width:  5%;">可調劑次數</th>
			<th style="width: 10%;">處方箋外送次數</th>
			<th style="width:  8%;">藥吃完日</th>
			<th style="width: 10%;">處方箋上傳(可復選)</th>
			<th style="width:  8%;">處方箋取得方式</th>
			<th style="width: 10%;">複製/刪除</th>
			<th style="width:  4%;">備註</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<!-- id -->
			<td>

			</td>

			<!-- 住民 -->
			<td>
				<input name="" class="span12" type="text" />
			</td>

			<!-- 身分證字號 -->
			<td>
				<input name="" class="span12" type="text" />
			</td>

			<!-- 生日 -->
			<td>
				<input name="" class="span12" type="text" />
			</td>

			<!-- 就醫日期 -->
			<td>
				<input name="" class="span12" type="text" />
			</td>

			<!-- 處方箋天數 -->
			<td>
				<input name="" class="span12" type="text" />
			</td>

			<!-- 可調劑次數 -->
			<td>
				<select name="" class="span12">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
				</select>
			</td>

			<!-- 外送次數 -->
			<td>
				<label for="item-x-prescription-time-1">1</label>
				<input id="item-x-prescription-time-1" class="js-prescription-time-1" type="checkbox" value="1" />
				<label for="item-x-prescription-time-2">2</label>
				<input id="item-x-prescription-time-2" class="js-prescription-time-2" type="checkbox" value="2" />
				<label for="item-x-prescription-time-3">3</label>
				<input id="item-x-prescription-time-3" class="js-prescription-time-3" type="checkbox" value="3" />
			</td>

			<!-- 藥吃完日 -->
			<td>
				<span class="first-time" style="display: inline-block;">
					<!-- js -->
				</span>
				<span class="second-time" style="display: inline-block;">
					<!-- js -->
				</span>
			</td>

			<!-- 處方箋上傳 -->
			<td>
				<!-- file -->
			</td>

			<!-- 處方箋取得方式 -->
			<td>
				<select class="span12 form-control js-status">
					<option value="original" selected>正本</option>
					<option value="drug-dealer-photo">藥師拍照</option>
					<option value="fax">傳真</option>
					<option value="line">line</option>
					<option value="upload">上傳檔案</option>
				</select>
			</td>

			<!-- 複製/刪除 -->
			<td>
				<!-- js -->
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" id="ButtonCopy" class="btn btn-default btn-sm" title="Suchen">
							<span class="glyphicon glyphicon-file"></span>
						</button>
						<button type="button" class="btn btn-default btn-sm js-ButtonDelete" title="Suchen">
							<span class="glyphicon glyphicon-trash"></span>
						</button>
					</div>
				</div>
			</td>

			<!-- 備註 -->
			<td>
				<textarea></textarea>
			</td>
		</tr>
		</tbody>
	</table>
</form>
