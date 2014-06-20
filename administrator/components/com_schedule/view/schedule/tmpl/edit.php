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
 * @var $asset           Windwalker\Helper\AssetHelper
 */
$container      = $this->getContainer();
$formInstitute  = $data->formInstitute;
$formIndividual = $data->formIndividual;
$asset          = $container->get('helper.asset');
$input          = $container->get('input');
$tmpl           = $input->get('tmpl');

if ('component' === $tmpl)
{
	// Fix styles when layout in modal box
	$asset->internalCSS('
		#adminForm {
			margin: 0;
		}
		.form-horizontal .control-group {
			margin-bottom: 6px;
		}
		.nav {
			margin-bottom: 10px;
		}
	');
}

$asset->addJS('schedule/edit.js');

$jsOptions = array(
	'schedulesUrl' => JRoute::_('index.php?option=com_schedule&view=schedules', false),
	'instituteApi' => JRoute::_('index.php?option=com_schedule&task=institute.ajax.json&id=', false),
	'membersApi' => JRoute::_('index.php?option=com_schedule&task=members.ajax.json&id=', false),
	'addressesApi' => JRoute::_('index.php?option=com_schedule&task=addresses.ajax.json&id=', false),
);
?>
<!-- Validate Script -->
<script type="text/javascript">
	jQuery(function()
	{
		ScheduleEdit.run(<?php echo json_encode($jsOptions); ?>);
	});

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
	};
</script>

<div id="schedule" class="windwalker schedule edit-form row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data">

		<div class="form-horizontal">
			<?php if ('component' === $tmpl): ?>
			<div class="pull-right">
				<button type="button" class="btn btn-success" onclick="Joomla.submitbutton('schedule.edit.save');">
					儲存
				</button>
				<button type="button" class="btn btn-danger" onclick="parent.closeModal('#modal-add-new-item');">
					取消
				</button>
			</div>
			<?php endif; ?>

			<?php echo JHtmlBootstrap::startTabSet('scheduleEditTab', array('active' => 'schedule_institute')); ?>

			<?php echo JHtmlBootstrap::addTab('scheduleEditTab', 'schedule_institute', '機構'); ?>
				<?php foreach ($formInstitute->getFieldset('basic') as $field): ?>
				<div id="control_<?php echo $field->id; ?>">
					<?php echo $field->getControlGroup() . "\n\n"; ?>
				</div>
				<?php endforeach;?>

				<p>
					<a href="#" id="institute-schedules-with-range" target="_blank">
						查詢前後七日排程
						<span class="glyphicon glyphicon-share-alt"></span>
					</a>
				</p>
			<?php echo JHtmlBootstrap::endTab(); ?>

			<?php echo JHtmlBootstrap::addTab('scheduleEditTab', 'schedule_individual', '散客'); ?>
				<?php foreach ($formIndividual->getFieldset('basic') as $field): ?>
				<div id="control_<?php echo $field->id; ?>">
					<?php echo $field->getControlGroup() . "\n\n"; ?>
				</div>
				<?php endforeach;?>

				<p>
					<a href="#" id="individual-schedules-with-range" target="_blank">
						查詢前後七日排程
						<span class="glyphicon glyphicon-share-alt"></span>
					</a>
				</p>
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
