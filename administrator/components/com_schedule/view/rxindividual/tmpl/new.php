<?php

$basic            = $data->form->getFieldset("basic");
$schedules["1st"] = $data->form->getFieldset("schedules_1st");
$schedules["2nd"] = $data->form->getFieldset("schedules_2nd");
$schedules["3rd"] = $data->form->getFieldset("schedules_3rd");
$ps               = $data->form->getFieldset("schedules_ps");
?>

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
			<div id="schedules_<?php echo $key; ?>" class="row-fluid">
				<!-- TODO: label 排列方式 -->

				<div class="col-lg-1">
					<?php echo $schedules[$key]['jform_deliver_nths']->getControlGroup(); ?>
				</div>
				<div class="col-lg-11">
					<div class="row-fluid">
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