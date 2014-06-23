<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/** @var JForm $printForm */
$printForm = $data->printForm;
?>

<form id="adminPrintScheduleReportForm"
	action="<?php echo JUri::getInstance(); ?>"
	method="post"
	target="_parent"
	class="form-validate form-horizontal">
<?php foreach ($printForm->getFieldset('schedules_print') as $field): ?>
	<div class="col-sm-offset-3 control-group">
		<div class="col-sm-2 control-label">
			<?php echo $field->label; ?>
		</div>
		<div class="col-sm-7 controls-<?php echo $field->id; ?>">
			<?php echo $field->input; ?>
		</div>
	</div>
<?php endforeach; ?>

	<!-- Hidden Inputs -->
	<div class="hidden-inputs">
		<input type="hidden" name="option" value="com_schedule" />
		<input type="hidden" name="layout" value="report" />
		<input type="hidden" name="view" value="schedules" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
