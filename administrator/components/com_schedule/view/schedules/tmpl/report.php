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

$ScheduleReport = new \Schedule\Helper\ScheduleReportHelper();
$items = $ScheduleReport->getData();
?>
<div id="schedule" class="windwalker schedule edit-form row-fluid" >
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" target="_parent"
		class="form-validate" enctype="multipart/form-data">

		<?php echo $this->loadTemplate('form'); ?>

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
			foreach ($items as $item):
				$rowSpan = count($item['institutes']) + 1;
				foreach ($item['institutes'] as $institute):
					$rowSpanRepeat++;
			?>
				<tr class="report-row">
					<!-- City -->
					<td class="left">
						<?php echo $item['city_title'];?>
						<?php echo $rowSpan?>
					</td>

					<!-- Institute or individual -->
					<td class="left">
						<?php echo $institute['title'];?>
					</td>

					<?php for($month = 1; $month <= 12; $month ++):?>
					<!-- Month -->
					<th class="right">
						<?php echo $month;?>月
					</th>
					<?php endfor;?>

					<!-- All this year -->
					<td class="right">
						all year
					</td>

					<!-- All total of the city-->
					<td class="left">
						<?php echo $item['total'];?>
					</td>
					<?php
					if($rowSpanRepeat == $rowSpan)
					{
						$rowSpanRepeat = 0;
					}
					?>
				</tr>
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
