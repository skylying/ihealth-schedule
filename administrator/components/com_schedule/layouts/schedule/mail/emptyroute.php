<?php
/**
 * Part of ihealth project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

$data['schedules'] = JArrayHelper::getValue($displayData, 'schedules', array());
$data['ihealthSiteUrl'] = JArrayHelper::getValue($displayData, 'ihealthSiteUrl', '');

$nthDelivery = array('1st' => '第一次宅配', '2nd' => '第二次宅配', '3rd' => '第三次宅配');
?>
<!DOCTYPE html>
<html>
<body>
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="font-family: 微软雅黑; letter-spacing: 3px;"><!--first table-->
	<tr>
		<td valign="top">
			<table border="0" cellpadding="20" cellspacing="0" width="600" id="emailContainer" style="background-color: #f2f2f2;"><!--second table-->
				<tr>
					<td valign="top">
						<table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailBody" style="background-color: #FFFFFF; font-size: 15px;"><!--third table-->
							<?php foreach ($data['schedules'] as $schedule): ?>
							<tr>
								<td valign="top">
										<div>
											<a href="<?php echo $data['ihealthSiteUrl']; ?>" style="float: right;">
												<img style="width: 83px; height: 20px;" src="<?php echo JUri::root() . '/media/com_schedule/images/ihealth.png'; ?>" />
											</a>
											<h2 style="letter-spacing: 3px;">
												<?php echo $schedule->member_name; ?>
												<br />
											 	預約了一筆無外送藥師的送藥路線。
											</h2>
										</div>
										<hr />

										<h3 style="letter-spacing: 3px;">資料如下:</h3>

										<table style="line-height: 1.42857143; vertical-align: top; font-family: 微软雅黑;"><!--fourth table-->
											<tr>
												<td>第幾次宅配:</td>
												<td style="padding: 10px;"><?php echo $nthDelivery[$schedule->deliver_nth]; ?></td>
											</tr>
											<tr>
												<td>會員姓名:</td>
												<td style="padding: 10px;"><?php echo $schedule->member_name; ?></td>
											</tr>
											<tr>
												<td>客戶姓名:</td>
												<td style="padding: 10px;"><?php echo $schedule->customer_name; ?></td>
											</tr>
											<tr>
												<td>客戶地址:</td>
												<td style="padding: 10px;"><?php echo $schedule->city_title . $schedule->area_title . $schedule->address; ?></td>
											</tr>
											<tr>
												<td>外送日:</td>
												<td style="padding: 10px;"><?php echo $schedule->date; ?></td>
											</tr>
											<tr>
												<td>宅配時段:</td>
												<td style="padding: 10px;"><?php echo JText::_('COM_SCHEDULE_SEND_SESSION_' . $schedule->session); ?></td>
											</tr>
											<tr>
												<td>手機:</td>
												<td style="padding: 10px;"><?php echo $schedule->mobile; ?></td>
											</tr>
											<tr>
												<td>辦公室:</td>
												<td style="padding: 10px;"><?php echo $schedule->tel_office; ?></td>
											</tr>
											<tr>
												<td>住家:</td>
												<td style="padding: 10px;"><?php echo $schedule->tel_home; ?></td>
											</tr>
										</table><!--end fourth table-->
										<div style="padding-top: 20px;">
											<a href="<?php echo JUri::root() . 'index.php?option=com_schedule&view=routes'; ?>">進入後台路線管理</a>
										</div>
									<?php endforeach; ?>
								</td>
							</tr>
						</table>
						<!--end third table-->
					</td>
				</tr>
				<tr>
					<td valign="top">
						<table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailFooter" style="font-size: 15px;"><!--fifth table-->
							<tr>
								<td valign="top" style="background: #3D8C12;">
									<div style="padding: 5px;">
										<div style="padding:3px; color:#FFFFFF;">
											<p>24hr免費諮詢專線: 0800-088-336</p>
											<p>政昇處方宅配藥局</p>
										</div>
										<div style="padding: 5px;">
											<h2 style="float: left; color: #FFFFFF;">
												藥師親自宅配、最方便、最放心。
											</h2>
										</div>
									</div>
								</td>
							</tr>
						</table>
						<!--end fifth table-->
					</td>
				</tr>
			</table>
			<!--end second table-->
		</td>
	</tr>
</table>
<!--end first table-->
</body>
</html>
