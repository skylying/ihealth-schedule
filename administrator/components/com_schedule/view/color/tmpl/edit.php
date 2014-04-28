<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JHtmlBootstrap::tooltip();
JHtmlFormbehavior::chosen('select');
JHtmlBehavior::formvalidation();

/**
 * Prepare data for this template.
 *
 * @var $container Windwalker\DI\Container
 * @var $data      Windwalker\Data\Data
 */
$container = $this->getContainer();
$form      = $data->form;

$fieldsets = $data->form->getFieldsets();
$fieldset = $fieldsets['information'];

$doc = JFactory::getDocument();
$doc->addStyleDeclaration('
#jform_colorpicker
{
	width: 100px;
	height: 30px;
}
')

?>
<!-- Validate Script -->
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'color.edit.cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<div id="schedule" class="windwalker color edit-form row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>"  method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data">
		<div class="row-fluid">
			<div class="span12">
				<fieldset id="color-edit-fieldset-<?php echo $fieldset->name ?>" class="form-horizontal">
					<legend>顏色資訊</legend>
					<?php foreach ($data->form->getFieldset($fieldset->name) as $field): ?>
						<div id="control_<?php echo $field->id; ?>">
							<?php echo $field->getControlGroup() . "\n\n"; ?>
						</div>
					<?php endforeach;?>
				</fieldset>
			</div>
		</div>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>

