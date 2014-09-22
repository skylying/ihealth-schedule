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
$infoFieldset = $fieldsets['information'];
$relatedCustomersFieldset = $fieldsets['related_customers'];
?>

<script>
	(function($)
	{
		$(document).ready(function()
		{
			var generateMailBtn = $('#generate-mail-btn'),
				fakeMailContainer = $('#fake-mail');

			generateMailBtn.on('click', function()
			{
				fakeMailContainer.animate({"opacity":1}, 800);
			})
		})
	})(jQuery)
</script>

<div id="schedule" class="windwalker member edit-form row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>"  method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data" autocomplete="off">
		<div class="row-fluid">
			<div class="span6">
				<fieldset id="member-edit-fieldset-<?php echo $infoFieldset->name ?>" class="form-horizontal">
					<?php foreach ($data->form->getFieldset($infoFieldset->name) as $field): ?>
						<div id="control_<?php echo $field->id; ?>">
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
									<?php if ('password' === $field->fieldname): ?>
										<div class="text-danger">
											<small>密碼至少需要輸入 4 個字元</small>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach;?>

					<?php if ($data->hasFakeMail): ?>
					<div class="control-group">
						<div class="control-label">
							<label for="generate-mail-btn" class="hasTooltip" data-original-title="<strong>產生假信箱</strong>">產生假信箱</label>
						</div>
						<div class="controls">
							<button id="generate-mail-btn" class="btn btn-info" type="button">點此產生</button>
							<span id="fake-mail" class="alert alert-info" style="opacity:0;"><?php echo isset($data->fakeMail) ? $data->fakeMail : ''; ?></span>
						</div>
					</div>
					<?php endif; ?>

				</fieldset>
			</div>
			<div class="span6">
				<fieldset id="member-edit-fieldset-<?php echo $relatedCustomersFieldset->name ?>" class="form-horizontal">
					<?php foreach ($data->form->getFieldset($relatedCustomersFieldset->name) as $field): ?>
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
