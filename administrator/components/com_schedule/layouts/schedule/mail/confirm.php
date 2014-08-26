<?php
/**
 * Part of ihealth project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

$data = $displayData;

$nthDelivery = array('第一次宅配', '第二次宅配', '第三次宅配');

?>
<!DOCTYPE html>
<html>
<body>
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="font-family: 微软雅黑; letter-spacing: 2px;"><!--first table-->
	<tr>
		<td valign="top">
			<table border="0" cellpadding="20" cellspacing="0" width="600" id="emailContainer" style="background-color:#f2f2f2;"><!--second table-->
				<tr>
					<td valign="top">
						<table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailBody" style="background-color: #FFFFFF; font-size: 15px;"><!--third table-->
							<tr>
								<td align="left" valign="top">
									<a href="http://www.ihealth.com.tw" style="float: right;">
										<img style="width: 83px; height: 20px;" src="<?php echo JUri::root() . '/media/com_schedule/images/ihealth.png'; ?>" />
									</a>
									<h2 style="letter-spacing: 2px;">您好!</h2>
									<h2 style="letter-spacing: 2px;">
										以下是<?php echo $data['rx']['member_name']; ?> 先生/小姐的預約宅配資料
									</h2>
									<hr />
									<table style="line-height: 1.42857143; vertical-align: top;"><!--fourth table-->
										<tr>
											<td style="padding-right: 10px;">處方姓名:</td>
											<td style="padding:10px;"><?php echo $data['rx']['customer_name']; ?></td>
										</tr>
										<tr>
											<td style="padding-right: 10px;">身分證字號:</td>
											<td style="padding:10px;"><?php echo $data['rx']['id_number']; ?></td>
										</tr>
										<?php foreach ($data['schedules'] as $key => $schedule): ?>
											<h3 style="font-family: 微软雅黑;"><?php echo $nthDelivery[$key]; ?></h3>
											<tr>
												<td style="padding-right: 10px;">宅配編號:</td>
												<td style="padding:10px;"><?php echo $schedule->id; ?></td>
											</tr>
											<tr>
												<td style="padding-right: 10px;">藥師送藥日期:</td>
												<td style="padding:10px;"><?php echo $schedule->date; ?></td>
											</tr>
											<tr>
												<td style="padding-right: 10px;">藥師送藥時段:</td>
												<td style="padding:10px;"><?php echo JText::_('COM_SCHEDULE_SEND_SESSION_' . $schedule->session); ?></td>
											</tr>
											<tr>
												<td style="padding-right: 10px;">藥師送藥地址:</td>
												<td style="padding:10px;"><?php echo $schedule->city_title . '' . $schedule->area_title . '' . $schedule->address; ?></td>
											</tr>
										<?php endforeach; ?>
									</table>
									<!--end fourth table-->
									<h3 style="font-family: 微软雅黑;">處方箋詳細資訊</h3>

									<table style=" line-height: 1.42857143; vertical-align: top; font-size: 15px;"><!--fifth table-->
										<tr>
											<td>就醫日期:</td>
											<td style="padding:10px;"><?php echo $data['rx']['see_dr_date']; ?></td>
										</tr>
										<tr>
											<td>處方箋傳送方式:</td>
											<td style="padding:10px;"><?php echo JText::_('COM_SCHEDULE_RXINDIVIDUAL_PRINT_' . $data['rx']['method']); ?></td>
										</tr>
										<tr>
											<td>可調劑次數:</td>
											<td style="padding:10px;"><?php echo $data['rx']['times']; ?></td>
										</tr>
										<tr>
											<?php foreach ($data['drugs'] as $key => $drug): ?>
												<?php if ($key == '0')
												{
													echo '<tr><td>藥品健保碼</td><td style="padding:10px;">' . $drug->hicode . '</td></tr>';
												}
												else
												{
													echo '<tr><td></td><td style="padding:10px;">' . $drug->hicode . '</td></tr>';
												}
												?>
											<?php endforeach; ?>
										</tr>
									</table>
									<!--end fifth table-->
									<h4 style="color:#1F00FF; font-family: 微软雅黑;">小叮嚀：外送藥師拜訪時, 請準備好您的健保卡
									</h4>
								</td>
							</tr>
						</table>
						<!--end third table-->
					</td>
				</tr>
				<tr>
					<td valign="top">
						<table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailFooter" style="font-size: 15px;"><!--sixth table-->
							<tr>
								<td valign="top" style="background: #3D8C12;">
									<div style="padding: 5px;">
										<div style="padding:3px; color:#FFFFFF;">
											<p>24hr免費諮詢專線: 0800-088-336/p>
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
						<!--end sixth table-->
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
