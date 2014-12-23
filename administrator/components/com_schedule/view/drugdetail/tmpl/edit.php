<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JHtmlFormbehavior::chosen('select');

/* Remove for potential js error cause by Joomla 3.3 update, 2014/8/14, Tim */
//JHtmlBehavior::formvalidation();

/**
 * Prepare data for this template.
 *
 * @var $container Windwalker\DI\Container
 * @var $data      Windwalker\Data\Data
 * @var $item      \stdClass
 */
$container      = $this->getContainer();
$form           = $data->form;
$filterForm     = $data->filterForm;
$item           = $data->item;
$isSaveAndPrint = $data->print;

$doc = JFactory::getDocument();

$asset = $container->get('helper.asset');
$asset->addJs('library/jquery.touchSwipe.js');
?>

<!-- Validate Script -->
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		// Delete empty extra purchase inputs
		window.InstituteExtraObject.deleteEmptyPrice();

		Joomla.submitform(task, document.getElementById('adminForm'));
	};

	// If the button action is to print
	<?php if ($isSaveAndPrint == '1'): ?>
		jQuery(document).ready(function()
		{
			var Institute = new InstituteExtra();

			// Print the window
			Institute.doPrint();
		});
	<?php endif; ?>
</script>

<style>
	@media print
	{
		.subhead-collapse
		{
			display: none;
		}

		.header
		{
			display: none;
		}

		a[href]:after {
			content: none !important;
		}
	}
</style>

<div id="schedule" class="windwalker drugdetails edit-form row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>"  method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data">

		<?php echo $this->loadTemplate("basic", array('tab' => "row")); ?>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>

		<div class="hide">
			<?php foreach($filterForm->getFieldset('filter') as $field): ?>
				<?php echo $field->input; ?>
			<?php endforeach; ?>
		</div>
		<input type="hidden" name="save-and-print" value="0"/>
	</form>
</div>

