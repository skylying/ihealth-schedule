<?php
/**
 * Part of ihealth project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
?>
<style>
	h2 {
		font-family : Tahoma, Helvetica, Arial, "Microsoft Yahei", "微软雅黑", STXihei, "华文细黑", sans-serif;
	}
</style>
<!DOCTYPE html>
<html>
<head>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	<title>
		通知:客戶預約了一筆無外送藥師的送藥路線。
	</title>
</head>

<div class="container">
	<div class="row">
		<div>
			<img src="<?php echo JUri::root(true) . '/media/media/images/ihealth.png' ?>" class="img-responsive" alt="Responsive image" />
		</div>
	</div>
</div>

<div class="row">
	<h2 class="text-center"><span><!--Member name--></span>預約了一筆無外送藥師的送藥路線。</h2>
</div>

<h3>資料如下:</h3>

<div class="row">
	<table class="table table-striped">
		<tr>
			<td>第幾次宅配:</td>
			<td><!--deliver nth--></td>
		</tr>
		<tr>
			<td>會員姓名:</td>
			<td><!--Member Name--></td>
		</tr>
		<tr>
			<td>客戶姓名:</td>
			<td><!--Customer Name--></td>
		</tr>
		<tr>
			<td>客戶地址:</td>
			<td><!--Customer Address--></td>
		</tr>
		<tr>
			<td>外送日:</td>
			<td><!--Date--></td>
		</tr>
		<tr>
			<td>宅配時段:</td>
			<td><!--Session--></td>
		</tr>
	</table>
	<table class="table table-bordered">
		<tr>
			<th class="text-center">聯絡電話</th>
		</tr>
		<tr>
			<td>手機:</td>
			<td><!--Mobile--></td>
		</tr>
		<tr>
			<td>辦公室:</td>
			<td><!--Tel Office--></td>
		</tr>
		<tr>
			<td>住家:</td>
			<td><!--Tel Home--></td>
		</tr>
	</table>
</div>
