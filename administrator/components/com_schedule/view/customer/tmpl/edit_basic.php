<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$tab       = $data->tab;
$fieldsets = $data->form->getFieldsets();

$typeField = $data->form->getField('type');
$customerType = $data->item->type;

?>

<div class="row-fluid">
	<div class="span6">
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['edit'], 'class' => 'form-horizontal')); ?>
	</div>

	<div class="span6">
		<?php echo $typeField->input; ?>
		<div id="individualdiv" class="<?php echo $customerType == 'individual' ? '' : 'hide'; ?>">
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['rxindividual'], 'class' => 'form-horizontal')); ?>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['address'], 'class' => 'form-horizontal')); ?>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['phones'], 'class' => 'form-horizontal')); ?>
		</div>
		<div id="residentdiv" class="<?php echo $customerType == 'resident' ? '' : 'hide'; ?>">
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['institute'], 'class' => 'form-horizontal')); ?>
		</div>

	</div>
</div>

<script type="text/javascript">

	(function($)
	{
		$('.customertype input').on('click', function()
		{
			var individualDiv = $('#individualdiv'),
				residentDiv   = $('#residentdiv');

			if($(this).val() == 'individual')
			{
				individualDiv.removeClass('hide');
				residentDiv.addClass('hide');
			}
			else
			{
				individualDiv.addClass('hide');
				residentDiv.removeClass('hide');
			}
		})

	})(jQuery);

</script>
