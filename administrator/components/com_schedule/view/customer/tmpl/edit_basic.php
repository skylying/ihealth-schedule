<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$tab       		= $data->tab;
$fieldsets 		= $data->form->getFieldsets();
$typeField 		= $data->form->getField('type');
$customerType 	= $data->item->type;

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
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['office'], 'class' => 'form-horizontal')); ?>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['home'], 'class' => 'form-horizontal')); ?>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['mobile'], 'class' => 'form-horizontal')); ?>
		</div>
		<div>

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
		});

 		var telOffice = jQuery('#jform_tel_office').val(),
			telHome = jQuery('#jform_tel_home').val(),
			telMobile = jQuery('#jform_mobile').val(),

			jsonTelO = jQuery.parseJSON(telOffice),
			jsonTelH = jQuery.parseJSON(telHome),
			jsonTelM = jQuery.parseJSON(telMobile);

		update = function(){

			jsonTelO = [
				{ "default" : jQuery('#radiojform_tel_office0').is(':checked'), "number" :  jQuery('#jform_tel_office0').val()},
				{ "default" : jQuery('#radiojform_tel_office1').is(':checked'), "number" :  jQuery('#jform_tel_office1').val()},
				{ "default" : jQuery('#radiojform_tel_office2').is(':checked'), "number" :  jQuery('#jform_tel_office2').val()}
			];

			jsonTelH = [
				{ "default" : jQuery('#radiojform_tel_home0').is(':checked'), "number" :  jQuery('#jform_tel_home0').val()},
				{ "default" : jQuery('#radiojform_tel_home1').is(':checked'), "number" :  jQuery('#jform_tel_home1').val()},
				{ "default" : jQuery('#radiojform_tel_home2').is(':checked'), "number" :  jQuery('#jform_tel_home2').val()}
			];

			jsonTelM = [
				{ "default" : jQuery('#radiojform_mobile0').is(':checked'), "number" :  jQuery('#jform_mobile0').val()},
				{ "default" : jQuery('#radiojform_mobile1').is(':checked'), "number" :  jQuery('#jform_mobile1').val()},
				{ "default" : jQuery('#radiojform_mobile2').is(':checked'), "number" :  jQuery('#jform_mobile2').val()}
			];

			jQuery('#jform_tel_office').val(JSON.stringify(jsonTelO)),
			jQuery('#jform_tel_home').val(JSON.stringify(jsonTelH)),
			jQuery('#jform_mobile').val(JSON.stringify(jsonTelM));

		}

		jQuery('#customer-edit-fieldset-office, #customer-edit-fieldset-home, #customer-edit-fieldset-mobile input').each(function(){
			jQuery(this).on('change', update );
		});

		//calculate age
		jQuery('#jform_birth_date').on('focusout', function()
		{
			var birthday = (new Date(jQuery(this).val())).getFullYear();

			var now = (new Date()).getFullYear();

			var age = now - birthday;

			jQuery('#jform_age').val(age);

		});
	})(jQuery);

</script>
