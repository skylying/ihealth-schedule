<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Windwalker\Data\Data;
use Windwalker\View\Layout\FileLayout;

JHtmlBehavior::multiselect('adminForm');

$tmpl = JUri::getInstance()->getVar('tmpl');
$filterFormLayout = new FileLayout('schedule.schedules.report_form');
?>

<script>
	/**
	 * Trigger auto select table
	 *
	 * @param containerid
	 */
	function selectText(containerid)
	{
		if (window.getSelection)
		{
			var range = document.createRange();
			range.selectNode(document.getElementById(containerid));
			window.getSelection().addRange(range);
		}
	}
</script>

<div id="schedule" class="windwalker schedule edit-form row-fluid" >
<?php if ($tmpl != 'component'): ?>
	<div style="width:90%; text-align:right;">
		<button type="button" class="btn btn-primary" onclick="jQuery('#print-schedule-report-form').submit();">
			<span class="glyphicon glyphicon-filter"></span>
			送出條件
		</button>
	</div>

	<div class="col-sm-offset-3">
		<?php
		echo $filterFormLayout->render(
			new Data(['printForm' => $data->printForm, 'formId' => 'print-schedule-report-form'])
		);
		?>
	</div>

	<hr style="border:0; height:1px; background-color:#000000;">

	<!-- LIST TABLE -->

	<div class="container">
		<div class="row">
			<div class="col-md-3 col-md-offset-1">
				<button class="btn btn-info" type="button" onclick="selectText('schedulereportList');">
					全選整份報表
				</button>
			</div>
			<div class="col-md-3 col-md-offset-5">
				<a href="<?php echo JURI::getInstance(); ?>&tmpl=component" type="button" class="btn btn-success" target="_blank">
					<span class="glyphicon glyphicon-print"></span>
					列印
				</a>
			</div>
		</div>
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

			<!-- AREA -->
			<th class="left">
				區域
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
				排程總合
			</th>
		</tr>
		</thead>
		<!-- TABLE BODY -->
		<tbody>

		<?php
		foreach ($item as $cityItem):
			$cityRepeat = 1;
			$cityAmount = count($cityItem['areas']);
			$instituteSubAmount = 0;

			foreach ($cityItem['areas'] as $areaSubItem):
				$instituteSubAmount = $instituteSubAmount + count($areaSubItem['institutes']);
			endforeach;

			$cityRowSpan = $cityAmount + $instituteSubAmount;

			foreach ($cityItem['areas'] as $areaItem):
				$instituteAmount = count($areaItem['institutes']);
				$areaRowSpan = $instituteAmount + 1;
				$areaRepeat = 1;

				foreach ($areaItem['institutes'] as $institute):
		?>
			<tr class="report-row">
				<!-- 縣市 -->
				<?php $citySpan = $cityRepeat++; ?>
				<?php if ($citySpan == 1):?>
				<td class="left" rowspan="<?php echo $cityRowSpan; ?>">
					<?php echo $cityItem['city_title']; ?>
				</td>
				<?php endif; ?>

				<!-- AREA -->
				<?php $areaSpan = $areaRepeat++;?>
				<?php if ($areaSpan == 1):?>
				<td class="left" rowspan="<?php echo $areaRowSpan; ?>">
					<?php echo $areaItem['area_title']; ?>
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

				<!-- 排程總合 -->
				<?php if ($citySpan == 1):?>
				<td class="left"  style="vertical-align: bottom;" rowspan="<?php echo $cityRowSpan; ?>">
					<?php echo $cityItem['total']; ?>
				</td>
				<?php endif; ?>
			</tr>
				<?php endforeach; ?>
			<!-- Customers -->
			<tr class="report-row">
				<!-- City -->
				<?php $citySpan = $cityRepeat++; ?>
				<?php if ($citySpan == 1):?>
				<td class="left" rowspan="<?php echo $cityRowSpan; ?>">
					<?php echo $cityItem['city_title']; ?>
				</td>
				<?php endif; ?>

				<!-- AREA -->
				<?php $areaSpan = $areaRepeat++; ?>
				<?php if ($areaSpan == 1):?>
				<td class="left" rowspan="<?php echo $areaRowSpan; ?>">
					<?php echo $areaItem['area_title']; ?>
				</td>
				<?php endif; ?>

				<!-- Customer -->
				<td class="left">
					散客
				</td>

				<?php for ($month = 0; $month <= 11; $month++): ?>
				<!-- 月 -->
				<td class="right">
					<?php echo $areaItem["customers"]["months"][$month]; ?>
				</td>
				<?php endfor; ?>

				<!-- 全年 -->
				<td class="right">
					<?php echo $areaItem["customers"]['sub_total']; ?>
				</td>

				<!-- 排程總合 -->
				<?php if ($citySpan == 1):?>
				<td class="left"  style="vertical-align: bottom;" rowspan="<?php echo $cityRowSpan; ?>">
					<?php echo $cityItem['total']; ?>
				</td>
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endforeach; ?>
</div>
