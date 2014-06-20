<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

$printForm = $data->printForm;
?>

<div class="form-horizontal">

	<div style="width:90%; text-align:right;">
		<button type="submit" class="btn btn-primary">
			<span class="glyphicon glyphicon-filter"></span>
			送出條件
		</button>
	</div>

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

</div>
