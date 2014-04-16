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
 * @var $container       Windwalker\DI\Container
 * @var $data            Windwalker\Data\Data
 * @var $formInstitute   JForm
 * @var $formIndividual  JForm
 */
$container      = $this->getContainer();
$formInstitute  = $data->formInstitute;
$formIndividual = $data->formIndividual;
?>
<!-- Validate Script -->
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		(function ($)
		{
			// Get selected group name
			var $formType = $('input[name="form_type"]'),
				$tabContent = $('#scheduleEditTabContent');

			$formType.val($tabContent.find('.active').attr('id'));

			// Remove in-active tab
			$tabContent.find('.tab-pane').each(function()
			{
				if (! $(this).hasClass('active'))
				{
					$(this).remove();
				}
			});
		})(jQuery);

		if (task == 'schedule.edit.cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<div id="schedule" class="windwalker schedule edit-form row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data">

		<div class="form-horizontal">
			<?php echo JHtmlBootstrap::startTabSet('scheduleEditTab', array('active' => 'schedule_institute')); ?>

			<?php echo JHtmlBootstrap::addTab('scheduleEditTab', 'schedule_institute', '機構'); ?>
				<?php foreach ($formInstitute->getFieldset('basic') as $field): ?>
				<div id="control_<?php echo $field->id; ?>">
					<?php echo $field->getControlGroup() . "\n\n"; ?>
				</div>
				<?php endforeach;?>

				<a href="#">
					查詢前後七日排程
					<i class="glyphicon glyphicon-share-alt"></i>
				</a>
			<?php echo JHtmlBootstrap::endTab(); ?>

			<?php echo JHtmlBootstrap::addTab('scheduleEditTab', 'schedule_individual', '散客'); ?>
				<?php foreach ($formIndividual->getFieldset('basic') as $field): ?>
				<div id="control_<?php echo $field->id; ?>">
					<?php echo $field->getControlGroup() . "\n\n"; ?>
				</div>
				<?php endforeach;?>

				<a href="#">
					查詢前後七日排程
					<i class="glyphicon glyphicon-share-alt"></i>
				</a>
			<?php echo JHtmlBootstrap::endTab(); ?>

			<?php echo JHtmlBootstrap::endTabSet(); ?>
		</div>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" value="<?php echo $data->item->id; ?>" />
			<input type="hidden" name="form_type" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
