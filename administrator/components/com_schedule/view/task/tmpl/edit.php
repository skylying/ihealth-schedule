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
 * @var $item      \stdClass
 */
$container = $this->getContainer();
$form      = $data->form;
$item      = $data->item;

$fieldsets = $data->form->getFieldsets();
$fieldset = $fieldsets['information'];

$senderId = $form->getField('sender')->id;
$senderName = $form->getField('sender_name')->id;

?>
<!-- Validate Script -->
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'task.edit.cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}


	};

	jQuery(function()
	{
		var senderId = jQuery( "#" + "<?php echo $senderId?>" );
		var senderName = jQuery( '#<?php echo $senderName?>' );

		senderId.change( function(){
			senderName.val( jQuery(this).find("option:selected").text() );

		});
	})
</script>

<div id="schedule" class="windwalker task edit-form row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>"  method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data">
		<div class="row-fluid">
			<div class="span8">
				<fieldset id="sender-edit-fieldset-<?php echo $fieldset->name ?>" class="form-horizontal">
					<legend>
						<?php echo $fieldset->label ? JText::_($fieldset->label) : JText::_('COM_SCHEDULE_EDIT_FIELDSET_' . $fieldset->name); ?>
					</legend>

					<?php foreach ($data->form->getFieldset($fieldset->name) as $field): ?>
						<div id="control_<?php echo $field->id; ?>">
							<?php echo $field->getControlGroup() . "\n\n"; ?>
						</div>
					<?php endforeach;?>
				</fieldset>
			</div>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>

