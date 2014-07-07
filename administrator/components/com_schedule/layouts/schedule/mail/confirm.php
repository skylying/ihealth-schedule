<?php
/**
 * Part of ihealth project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

$data = $displayData;

?>
<!DOCTYPE html>
<html>
<body>
<head>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	<title>
		以下是您的預約宅配資料
	</title>
	<style>
		h2 {
			font-family : Tahoma, Helvetica, Arial, "Microsoft Yahei", "微软雅黑", STXihei, "华文细黑", sans-serif;
		}
	</style>
</head>

<div class="container">
	<div class="row">
		<div>
			<img src="<?php echo JUri::root(true) . '/media/com_schedule/images/ihealth.png' ?>" class="img-responsive" alt="Responsive image" />
		</div>
	</div>

	<div class="row">
		<h2 class="text-center"><span><?php echo $data->member->name; ?></span> 先生/小姐 <br /> 您好：以下是您的預約宅配資料 </h2>
		<h4 class="bg-success">小叮嚀：外送藥師拜訪時, 請準備好您的健保卡</h4>
		<?php foreach ($data['schedules'] as $schedule): ?>
			<?php echo '<h2>' . $nthDelivery[$schedule['deliver_nth']] . '</h2>'; ?>
			<table class="table table-striped">
				<tr>
					<td>宅配編號</td>
					<td><?php echo $schedule['id']; ?></td>
				</tr>
				<tr>
					<td>處方姓名</td>
					<td><?php echo $data->customer->name; ?></td>
				</tr>
				<tr>
					<td>身分證字號</td>
					<td><?php echo $data->customer->id_number; ?></td>
				</tr>
				<tr>
					<td>藥師送藥日期</td>
					<td><?php echo $schedule['date']; ?></td>
				</tr>
				<tr>
					<td>藥師送藥時段</td>
					<td><?php echo JText::_('COM_SCHEDULE_SEND_SESSION_' . $schedule['session']); ?></td>
				</tr>
				<tr>
					<td>藥師送藥地址</td>
					<td><?php echo $schedule['city_title'] . '' . $schedule['area_title'] . '' . $schedule['address']; ?></td>
				</tr>
			</table>
		<?php endforeach; ?>
	</div>

	<h2>處方箋詳細資訊</h2>

	<div class="row">
		<table class="table table-striped">
			<tr>
				<td>就醫日期</td>
				<td><?php echo $data['rx']['see_dr_date']; ?></td>
				<td>處方箋傳送方式</td>
				<td><?php echo JText::_('COM_SCHEDULE_RXINDIVIDUAL_PRINT_' . $data['rx']['method']); ?></td>
			</tr>
			<tr>
				<td>可調劑次數</td>
				<td><?php echo $data['rx']['times']; ?></td>
				<td>給藥天數</td>
				<td><?php echo $data['rx']['period']; ?></td>
			</tr>
		</table>
	</div>

	<div class="row" style="padding: 5px; background: #dff0d8;">
		<div class="col-md-6">
			24hr免費諮詢專線<br />
			0800-000-000<br />
			政昇處方宅配藥局<br />
			www.ihealth.com.tw<br />
		</div>
		<div class="col-md-6">
			<h2>藥師親自宅配. 最方便. 最放心.</h2>
		</div>
	</div>
</div>
</body>
</html>
