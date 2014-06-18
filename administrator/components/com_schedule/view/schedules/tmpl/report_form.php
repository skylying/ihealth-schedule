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
	<?php foreach ($printForm->getFieldset('schedules_print') as $field): ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo $field->label; ?>
			</div>
			<div class="controls-<?php echo $field->id; ?>">
				<?php echo $field->input; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<button type="submit" class="btn btn-primary">
	<span class="glyphicon glyphicon-filter"></span>
	送出條件
</button>

<script type="text/javascript">
	function check_all(obj,cName)
	{
		var checkboxes = document.getElementsByName(cName);
		for(var i=0;i<checkboxes.length;i++){checkboxes[i].checked = obj.checked;}
	}
</script>
