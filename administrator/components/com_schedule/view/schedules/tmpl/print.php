<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
JHTML::_('behavior.modal');

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
?>

<div id="schedule" class="windwalker schedule edit-form row-fluid" >
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" target="_parent"
		class="form-validate" enctype="multipart/form-data">

		<div class="form-horizontal">
			<?php foreach ($printForm->getFieldset('basic') as $field): ?>
			<div id="control_<?php echo $field->id; ?>">
				<?php echo $field->getControlGroup() . "\n\n"; ?>
			</div>
			<?php endforeach;?>
		</div>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="task" value="report" />
			<?php echo JHtml::_('form.token'); ?>
		</div>

		<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('schedules.report')">
			<span class="glyphicon glyphicon-print"></span>
				產生報表
		</button>

	</form>
</div>
