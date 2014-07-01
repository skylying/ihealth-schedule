<?php
/**
 * Part of ihealth project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
?>
<!DOCTYPE html>
<html>
<head>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	<title>
		以下是您的預約宅配資料
	</title>
</head>

<div class="container">
	<div class="row">
		<div>
			<img src="<?php echo JUri::root(true) . '/media/media/images/ihealth.png' ?>" class="img-responsive" alt="Responsive image" />
		</div>
	</div>

	<div class="row">
		<h2 class="text-center"><span><!--Customer Name--></span>先生/小姐 您好：以下是您的預約宅配資料</h2>
		<h4 class="bg-success">小叮嚀：外送藥師拜訪時, 請準備好您的健保卡</h4>
		<table class="table table-striped">
			<tr>
				<td>宅配編號</td>
				<td><!--Schedule id--></td>
			</tr>
			<tr>
				<td>處方姓名</td>
				<td><!--Customer name--></td>
			</tr>
			<tr>
				<td>身分證字號</td>
				<td><!--Id number--></td>
			</tr>
			<tr>
				<td>藥師送藥日期</td>
				<td><!--Date--></td>
			</tr>
			<tr>
				<td>藥師送藥時段</td>
				<td><!--Session--></td>
			</tr>
			<tr>
				<td>藥師送藥地址</td>
				<td><!--Full address--></td>
			</tr>
		</table>
	</div>

	<h3>處方箋詳細資訊</h3>

	<div class="row">
		<table class="table table-striped">
			<tr>
				<td>就醫日期</td>
				<td><!--see dr date--></td>
				<td>處方箋傳送方式</td>
				<td><!--method--></td>
			</tr>
			<tr>
				<td>可調劑次數</td>
				<td><!--Times--></td>
				<td>醫師姓名</td>
				<td><!--醫師姓名--></td>
			</tr>
			<tr>
				<td>第幾次領藥</td>
				<td><!--deliver nth--></td>
				<td>國際疾病代碼</td>
				<td><!--國際疾病代碼--></td>
			</tr>
			<tr>
				<td>給藥天數</td>
				<td><!--period--></td>
				<td>藥品健保碼</td>
				<td><!--hicode--></td>
			</tr>
		</table>
	</div>

	<div class="row" style="padding: 5px; background: #dff0d8; display: inline-block; width: 100%;">
		<div class="col-md-6">
			24hr免費諮詢專線<br />
			0800-000-000<br />
			政昇處方宅配藥局<br />
			www.ihealth.com.tw<br />
		</div>
		<div class="col-md-6">
			<h3>藥師親自宅配. 最方便. 最放心.</h3>
		</div>
	</div>
</div>
</html>
