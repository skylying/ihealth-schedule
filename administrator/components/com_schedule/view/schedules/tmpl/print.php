<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$formPrint = $data->formPrint;
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

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="task" value="viewReport" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
