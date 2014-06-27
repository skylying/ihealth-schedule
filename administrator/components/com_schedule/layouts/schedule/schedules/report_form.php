<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * @var Windwalker\Data\Data $displayData
 * @var JForm                $printForm
 */
$printForm = $displayData->printForm;
?>

<form id="<?php echo $displayData->formId; ?>"
	action="<?php echo JRoute::_('index.php'); ?>"
	method="get"
	class="form-validate form-horizontal">
<?php foreach ($printForm->getFieldset('schedules_print') as $field): ?>
	<div class="control-group">
		<div class="col-sm-3 control-label">
			<?php echo $field->label; ?>
		</div>
		<div class="col-sm-9 controls-<?php echo $field->id; ?>">
			<?php echo $field->input; ?>
		</div>
	</div>
<?php endforeach; ?>

	<div class="hidden-inputs">
		<input type="hidden" name="option" value="com_schedule" />
		<input type="hidden" name="view" value="schedules" />
		<input type="hidden" name="layout" value="report" />
	</div>
</form>
