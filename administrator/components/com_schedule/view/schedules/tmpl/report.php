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

$formPrint = $data->formPrint;

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

?>

<div id="schedule" class="windwalker schedule edit-form row-fluid" >
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" target="_parent"
		class="form-validate" enctype="multipart/form-data">

		<div class="form-horizontal">
			<?php foreach ($formPrint->getFieldset('basic') as $field): ?>
			<div id="control_<?php echo $field->id; ?>">
				<?php echo $field->getControlGroup() . "\n\n"; ?>
			</div>
			<?php endforeach;?>
		</div>

		<hr style="border:0; height:1px; background-color:#000000;">

		<!-- LIST TABLE -->
		<table id="schedulereportList" class="table table-striped adminlist">
			<!-- TABLE HEADER -->
			<thead>
			<tr>
				<!-- CITY -->
				<th class="left">
					縣市
				</th>

				<!-- Institute Title-->
				<th class="left">
					所屬機構
				</th>

				<!-- Date -->
				<th class="left">
					Date
				</th>

				<?php
				for($month = 1; $month < 12; $month ++):
				?>
				<!-- Month -->
				<th class="right">
					<?php echo $month?>月
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

				<!-- Type -->
				<th class="left">
					Type
				</th>
			</tr>
			</thead>
			<!-- TABLE BODY -->
			<tbody>
			<?php foreach ($data->items as $i => $item)
				:
				?>
				<tr class="schedulereport-row">
					<!-- City -->
					<?php
					$pastCity = $currentCity;
					$currentCity = $item->city_title;
					if($currentCity != $pastCity)
					{
						$firstCity = $currentCity;
					}
					else
					{
						$firstCity = '';
					}
					?>
					<td class="left">
						<?php echo $firstCity ?>
					</td>

					<!-- Institute Title-->
					<?php
					$pastInstitute = $currentInstitute;
					$currentInstitute = $item->institute_title;
					if($currentInstitute != $pastInstitute)
					{
						$firstInstitute = $currentInstitute;
					}
					else
					{
						$firstInstitute = '';
					}
					?>
					<td class="left">
						<?php echo $firstInstitute;?>
					</td>

					<!-- Date -->
					<td class="left">
						<?php echo $item->date;?>
					</td>

					<?php
					for($month = 1; $month < 12; $month ++):
					?>
					<!-- Month -->
					<td class="right">
						0
					</td>
					<?php endfor;?>

					<!-- All This Year -->
					<td class="right">
						0
					</td>

					<!-- Sub Total -->
					<td class="right">
						0
					</td>

					<!-- Type -->
					<td class="left">
						<?php echo $item->type ?>
					</td>
				</tr>
			<?php endforeach; ?>
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
