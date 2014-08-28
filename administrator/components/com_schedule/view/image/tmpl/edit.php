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
JHtmlBehavior::formvalidation();

/**
 * Prepare data for this template.
 *
 * @var $container Windwalker\DI\Container
 * @var $data      Windwalker\Data\Data
 * @var $form      JForm
 * @var $asset     Windwalker\Helper\AssetHelper
 */
$container = $this->getContainer();
$form      = $data->form;
$asset     = $data->asset;
$fieldSets = $form->getFieldsets();
$fieldSet  = $fieldSets['information'];

$asset->addJS('image/edit.js');

?>

<script type="text/javascript">
	jQuery(function($)
	{
		ImageEdit.run();
	});

	Joomla.submitbutton = function(task)
	{
		// Validation
		if (task == 'image.edit.cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<div id="schedule" class="windwalker image edit-form row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>"  method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data">
		<div class="row-fluid">
			<div class="span8">
				<fieldset id="image-edit-fieldset-<?php echo $fieldSet->name ?>" class="form-horizontal">
					<legend>圖片資訊</legend>
					<?php foreach ($form->getFieldset($fieldSet->name) as $field): ?>
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
