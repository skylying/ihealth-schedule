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
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="font-family: 微软雅黑;"><!--first table-->
	<tr>
		<td valign="top">
			<table border="0" cellpadding="20" cellspacing="0" width="600" id="emailContainer" style="background-color:#f2f2f2;"><!--second table-->
				<tr>
					<td valign="top">
						<table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailBody" style="background-color: #FFFFFF; font-size: 15px;"><!--third table-->
							<tr>
								<td valign="top">
									<?php foreach ($data['schedules'] as $schedule): ?>

									<div>
										<h2 style="font-family: 微软雅黑;">
											<?php echo $data->member->name; ?> 先生/小姐 <br />
											已取消日期: <?php echo $schedule['date']; ?> 送藥排程。
										</h2>
									</div>

									<h3 style="font-family: 微软雅黑;">資料如下:</h3>

									<div>
										<table><!--fourth table-->
											<tr>
												<td>第幾次宅配:</td>
												<td style="padding:10px;"><?php echo $nthDelivery[$schedule['deliver_nth']]; ?></td>
											</tr>
											<tr>
												<td>會員姓名:</td>
												<td style="padding:10px;"><?php echo $schedule['member_name']; ?></td>
											</tr>
											<tr>
												<td>客戶姓名:</td>
												<td style="padding:10px;"><?php echo $schedule['customer_name']; ?></td>
											</tr>
											<tr>
												<td>客戶地址:</td>
												<td style="padding:10px;"><?php echo $schedule['city_title'] . $schedule['area_title'] . $schedule['address']; ?></td>
											</tr>
											<tr>
												<td>外送排程日:</td>
												<td style="padding:10px;"><?php echo $schedule['date']; ?></td>
											</tr>
											<tr>
												<td>宅配時段:</td>
												<td style="padding:10px;"><?php echo JText::_('COM_SCHEDULE_SEND_SESSION_' . $schedule['session']); ?></td>
											</tr>
											<tr>
												<td>手機:</td>
												<td style="padding:10px;"><?php echo $schedule['mobile']; ?></td>
											</tr>
											<tr>
												<td>辦公室:</td>
												<td style="padding:10px;"><?php echo $schedule['tel_office']; ?></td>
											</tr>
											<tr>
												<td>住家:</td>
												<td style="padding:10px;"><?php echo $schedule['tel_home']; ?></td>
											</tr>
										</table>
										<!--end fourth table-->
										<a href="<?php echo JUri::root() . 'index.php?option=com_schedule&view=schedules'; ?>">進入後台送藥排程</a>
										<a href="http://www.ihealth.com.tw" style="float: right;">
											<img style="width: 83px; height: 20px;" src="<?php echo JUri::root() . '/media/com_schedule/images/ihealth.png'; ?>" />
										</a>
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
											<p>24hr免費諮詢專線: 0800-000-000</p>
											<p>政昇處方宅配藥局</p>
										</div>
										<div style="padding: 5px;">
											<h2 style="float: right; color: #FFFFFF;">
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
