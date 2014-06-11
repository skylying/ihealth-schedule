<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

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
<div class="form-horizontal">
	<?php foreach ($printForm->getFieldset('schedules_print') as $field): ?>
	<div id="control_<?php echo $field->id; ?>">
		<?php echo $field->getControlGroup(); ?>
	</div>
	<?php endforeach;?>
</div>

<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('schedules.report')">
	<span class="glyphicon glyphicon-filter"></span>
		送出條件
</button>
