<?php
/**
 * Part of ihealth project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

$data = $displayData;

$nthDelivery = array('1st' => '第一次宅配', '2nd' => '第二次宅配', '3rd' => '第三次宅配');
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
		通知:客戶預約了一筆無外送藥師的送藥路線。
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

	<?php foreach ($data['schedules'] as $schedule): ?>
	<div class="row">
		<h2 class="text-center"><span><?php echo $schedule['member_name']; ?></span> 預約了一筆無外送藥師的送藥路線。 </h2>
	</div>

	<h2>資料如下:</h2>

	<div class="row">
		<table class="table table-striped">
			<tr>
				<td class="text-center">第幾次宅配:</td>
				<td><?php echo $nthDelivery[$schedule['deliver_nth']]; ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="text-center">會員姓名:</td>
				<td><?php echo $schedule['member_name']; ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="text-center">客戶姓名:</td>
				<td><?php echo $schedule['customer_name']; ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="text-center">客戶地址:</td>
				<td><?php echo $data['route']->city_title . $data['route']->area_title . $data['route']->address; ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="text-center">外送日:</td>
				<td><?php echo $schedule['date']; ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="text-center">宅配時段:</td>
				<td><?php echo JText::_('COM_SCHEDULE_SEND_SESSION_' . $schedule['session']); ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="text-center">手機:</td>
				<td><?php echo $schedule['mobile']; ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="text-center">辦公室:</td>
				<td><?php echo $schedule['tel_office']; ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="text-center">住家:</td>
				<td><?php echo $schedule['tel_home']; ?></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	</div>
	<?php endforeach; ?>
</div>
</body>
</html>
