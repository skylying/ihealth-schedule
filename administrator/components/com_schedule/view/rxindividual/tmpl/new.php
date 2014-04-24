<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

$basic            = $data->form->getFieldset("basic");
$schedules["1st"] = $data->form->getFieldset("schedules_1st");
$schedules["2nd"] = $data->form->getFieldset("schedules_2nd");
$schedules["3rd"] = $data->form->getFieldset("schedules_3rd");
$ps               = $data->form->getFieldset("schedules_ps");

?>

<style>
	.schedules .control-label
	{
		float: none;
	}

	.schedules .controls
	{
		margin-left: 0;
	}

	.schedules input
	{
		width: 80%;
	}

	.schedules select
	{
		width: 100%;
	}

	.address label
	{
		display: none;
	}
</style>

<form class="form-horizontal">
	<div class="row-fluid">
		<div class="col-lg-5">
			<?php
			foreach ($basic as $field)
			{
				echo $field->getControlGroup();
			}
			?>
		</div>
		<div class="col-lg-5 col-lg-offset-1">
			<?php foreach (array("1st", "2nd", "3rd") as $key): ?>
			<div id="schedules_<?php echo $key; ?>" class="row-fluid schedules schedules_<?php echo $key; ?>">
				<div class="col-lg-3">
					<!-- TODO: 換成可愛的圓圈圈 -->
					<?php echo $schedules[$key]['jform_deliver_nths']->getControlGroup(); ?>
				</div>
				<div class="col-lg-9">
					<div class="row-fluid address">
						<div class="col-lg-12">
							<?php echo $schedules[$key]['jform_address']->getControlGroup(); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="col-lg-4">
							<!-- TODO:js -->
							<?php echo $schedules[$key]['jform_empty_date']->getControlGroup(); ?>
						</div>
						<div class="col-lg-4">
							<?php echo $schedules[$key]['jform_send_time']->getControlGroup(); ?>
						</div>
						<div class="col-lg-4">
							<?php echo $schedules[$key]['jform_send_date']->getControlGroup(); ?>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
			<div class="row-fluid well">
				<div class="col-lg-12">
					<!-- TODO:js -->
					<?php
					foreach ($ps as $field)
					{
						echo $field->getControlGroup();
					}
					?>
				</div>
			</div>
		</div>
	</div>
</form>