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
?>
<!-- Validate Script -->
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (CustomerJs.validateBirthday() == false)
		{
			Joomla.renderMessages( [['請確認生日格式是否符合8位數字或(YYYY-MM-DD)格式。']] );
		}
		else
		{
			// Remove unnecessary form
			CustomerJs.removeForm();

			// Update hidden json inputs (for phone numbers)
			CustomerJs.updatePhoneJson();

			// Update hidden json inputs (for addresses)
			CustomerJs.updateAddressJson();

			if (task == 'customer.edit.cancel' || document.formvalidator.isValid(document.id('adminForm')))
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		}
	}
</script>

<div id="schedule" class="windwalker customer edit-form row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>"  method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data">

		<?php echo $this->loadTemplate('basic'); ?>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>

