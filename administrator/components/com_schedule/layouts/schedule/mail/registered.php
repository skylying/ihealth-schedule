<?php
/**
 * Part of ihealth project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

$data['name'] = JArrayHelper::getValue($displayData, 'name', '');
$data['email'] = JArrayHelper::getValue($displayData, 'email', '');
$data['ihealthSiteUrl'] = JArrayHelper::getValue($displayData, 'ihealthSiteUrl', 'www.ihealth.com.tw');
$data['0800'] = JArrayHelper::getValue($displayData, '0800', '0800-088-336');

?>
<!DOCTYPE html>
<html>
<body>
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="font-family: "Helvetica Neue", Helvetica, Arial, sans-serif, 微软雅黑; letter-spacing: 3px;"><!--first table-->
	<tr>
		<td valign="top">
			<table border="0" cellpadding="20" cellspacing="0" width="600" id="emailContainer" style="background-color:#f2f2f2;"><!--second table-->
				<tr>
					<td valign="top">
						<table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailBody" style="background-color: #FFFFFF; font-size: 15px;"><!--third table-->
							<tr>
								<td valign="top">
									<div>
										<a href="<?php echo $data['ihealthSiteUrl']; ?>" style="float: right;">
											<img style="width: 83px; height: 20px;" src="<?php echo JUri::root() . '/media/com_schedule/images/ihealth.png'; ?>" />
										</a>
										<h2 style="letter-spacing: 2px;">
											<?php echo $data['name']; ?> 先生/小姐 <br />
										</h2>
										<h2>
											您已成功註冊iHealth服務。
										</h2>
									</div>
									<hr />
									<h3>資料如下:</h3>
									<div>
										<!--fourth table-->
										<table style="line-height: 1.42857143; vertical-align: top; font-family:"Helvetica Neue", Helvetica, Arial, sans-serif, 微软雅黑;">
											<tr>
												<td>會員姓名:</td>
												<td style="padding:10px;"><?php echo $data['name']; ?></td>
											</tr>
											<tr>
												<td>客戶信箱:</td>
												<td style="padding:10px;"><?php echo $data['email']; ?></td>
											</tr>
										</table>
										<br />
										<a href="<?php echo $data['ihealthSiteUrl'] . '/schedules?new=1'; ?>">馬上預約</a>。
										<!--end fourth table-->
									</div>
								</td>
							</tr>
						</table><!--end third table-->
					</td>
				</tr>
				<tr>
					<td valign="top">
						<table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailFooter" style="font-size: 15px;"><!--fifth table-->
							<tr>
								<td valign="top" style="background: #3D8C12;">
									<div style="padding: 5px;">
										<div style="padding:3px; color:#FFFFFF;">
											<p>24hr免費諮詢專線: <?php echo $displayData['0800']; ?></p>
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
