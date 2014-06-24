<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
JHtmlBehavior::multiselect('adminForm');
$tmpl = JUri::getInstance()->getVar('tmpl');
?>
<div id="schedule" class="windwalker schedule edit-form row-fluid" >
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" target="_parent"
		class="form-validate" enctype="multipart/form-data">

	<?php if ($tmpl != 'component'): ?>

		<?php echo $this->loadTemplate('form'); ?>

		<hr style="border:0; height:1px; background-color:#000000;">

		<!-- LIST TABLE -->

		<div style="width: 90%; text-align: right; padding-bottom: 20px;">
			<a href="<?php echo JURI::getInstance(); ?>&tmpl=component" type="button" class="btn btn-success" target="_blank">
				<span class="glyphicon glyphicon-print"></span>
				列印
			</a>
		</div>

	<?php else: ?>

		<script>
			print();
		</script>

	<?php endif; ?>

	<?php foreach ($data->items as $year => $item): ?>
		<h1 style="text-align: center"><?php echo $year; ?></h1>
		<table id="schedulereportList" class="table table-bordered adminlist">
			<!-- TABLE HEADER -->
			<thead>
			<tr>
				<!-- CITY -->
				<th class="left">
					縣市
				</th>

				<!-- Institute-->
				<th class="left">
					所屬機構
				</th>
				<?php for ($month = 1; $month <= 12; $month++): ?>
				<!-- Month -->
				<th class="right">
					<?php echo $month; ?>月
				</th>
				<?php endfor; ?>

				<!-- All This Year -->
				<th class="right">
					全年
				</th>

				<!-- Sub Total -->
				<th class="left">
					排程小計
				</th>
			</tr>
			</thead>
			<!-- TABLE BODY -->
			<tbody>

			<?php

			foreach ($item as $subItem):
				$instituteAmount = count($subItem['institutes']);

				// 重設表格的 rowSpanRepeat 讓表格的 rowSpan 數值出現給下個縣市印出
				$rowSpanRepeat = 0;

				$rowSpan = $instituteAmount + 1;

				foreach ($subItem['institutes'] as $institute):
					$rowSpanRepeat++;
			?>
				<!-- 列出 $item['institutes'] 的資料 -->
				<tr class="report-row">
					<!-- 縣市 -->
					<?php if ($rowSpanRepeat == 1): ?>
					<td class="left" rowspan="<?php echo $rowSpan; ?>">
						<?php echo $subItem['city_title']; ?>
					</td>
					<?php endif; ?>

					<!-- Institute -->
					<td class="left">
						<?php echo $institute['title']; ?>
					</td>

					<?php for ($month = 0; $month <= 11; $month++): ?>
					<!-- 月 -->
					<td class="right">
						<?php echo $institute["months"][$month]; ?>
					</td>
					<?php endfor; ?>

					<!-- 全年 -->
					<td class="right">
						<?php echo $institute['sub_total']; ?>
					</td>

					<?php if ($rowSpanRepeat == 1): ?>
					<!-- 排程小計 -->
					<td class="left" rowspan="<?php echo $rowSpan; ?>" style="vertical-align: bottom;">
						<?php echo $subItem['total']; ?>
					</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
				<!-- 列出 $item['customers'] 的資料 -->
				<tr class="report-row">
					<!-- 如果該縣市沒有機構，則補印縣市 -->
					<?php if ($instituteAmount == 0): ?>
					<td class="left">
						<?php echo $subItem['city_title']; ?>
					</td>
					<?php endif; ?>

					<!-- individual -->
					<td class="left">
						散客
					</td>

					<?php for ($month = 0; $month <= 11; $month ++): ?>
					<!-- 月 -->
					<td class="right">
						<?php echo $subItem['customers']['months'][$month]; ?>
					</td>
					<?php endfor; ?>

					<!-- 排程小計 -->
					<td class="left">
						<?php echo $subItem['customers']['sub_total']; ?>
					</td>

					<!-- 如果該縣市沒有機構，則補印散客的排程小計 -->
					<?php if ($instituteAmount == 0): ?>
					<td class="left">
						<?php echo $subItem['total']; ?>
					</td>
					<?php endif; ?>

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php endforeach; ?>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="layout" value="report" />
			<input type="hidden" name="view" value="schedules" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
