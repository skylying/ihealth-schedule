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

$printForm = $data->printForm;

$doc = JFactory::getDocument();
$css = <<<CSS
ol
{
	list-style-type:none;
}

ol li
{
	float:left;
	margin: 0 10px;
	padding: 0 10px;
}

ol li label
{
	float:right;
	display:inline;
	margin: 0 2px;
	padding: 0 2px;
}
CSS;

$doc->addStyleDeclaration($css);

$reportHelper = new \Schedule\Helper\ScheduleReportHelper();
$reports = $reportHelper->reportData();


?>

<div id="schedule" class="windwalker schedule edit-form row-fluid" >
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" target="_parent"
		class="form-validate" enctype="multipart/form-data">

		<div class="form-horizontal">
			<?php foreach ($printForm->getFieldset('basic') as $field): ?>
			<div id="control_<?php echo $field->id; ?>">
				<?php echo $field->getControlGroup(); ?>
			</div>
			<?php endforeach;?>
		</div>

		<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('schedules.report')">
			<span class="glyphicon glyphicon-filter"></span>
				送出條件
		</button>

		<hr style="border:0; height:1px; background-color:#000000;">

		<!-- LIST TABLE -->

		<button type="button" class="btn btn-success">
			<span class="glyphicon glyphicon-print"></span>
				列印
		</button>

		<table id="schedulereportList" class="table table-striped adminlist">
			<!-- TABLE HEADER -->
			<thead>
			<tr>
				<!-- CITY -->
				<th class="left">
					縣市
				</th>

				<!-- Institute or individual -->
				<th class="left">
					所屬機構
				</th>

				<?php
				for($month = 1; $month <= 12; $month ++):
				?>
				<!-- Month -->
				<th class="right">
					<?php echo $month;?>月
				</th>
				<?php endfor;?>

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
			$rowSpanRepeat = 0;
			$totalOfCity = 0;

			foreach ($reports as $keyCity => $belongs):
				$cityTitle = $keyCity;
				$rowSpan = count($belongs);

				foreach ($belongs as $keyBelong => $months):
					$belongTitle = $keyBelong;

					$rowSpanRepeat++;
			?>
				<tr class="report-row">
					<!-- City -->
					<?php if($rowSpanRepeat == 1):?>
					<td class="left" ROWSPAN="<?php echo $rowSpan;?>">
						<?php echo $cityTitle;?>
					</td>
					<?php endif;?>

					<!-- Institute or individual to belong -->
					<td class="left">
						<?php echo $belongTitle; ?>
					</td>

					<?php
					$allYearAmount = 0;
					foreach ($months as $keyMonth => $amounts): ?>
					<!-- Month -->
					<td class="right">
						<?php
						$monthAmount = array_sum($amounts);
						echo $monthAmount;
						$allYearAmount = $allYearAmount + $monthAmount;
						?>
					</td>
					<?php endforeach;?>

					<!-- All this year -->
					<td class="right">
						<?php echo $allYearAmount;?>
					</td>

					<!-- All total of the city-->
					<?php
					$totalOfCity = $allYearAmount + $totalOfCity;
					if($rowSpanRepeat == $rowSpan):
						$showTotal = $totalOfCity;
					else:
						$showTotal = '';
					endif;
					?>
					<td class="left">
						<?php echo $showTotal;?>
					</td>
				</tr>
					<?php
					if($rowSpanRepeat == $rowSpan):
						$rowSpanRepeat = 0;
						$totalOfCity = 0;
					endif;
					?>
				<?php endforeach;?>
			<?php endforeach;?>
			</tbody>
		</table>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="task" value="report" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
