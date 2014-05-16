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
$data->asset->addJS('moment-with-langs.min.js');
$data->asset->addJS('multi-row-handler.js');

?>
<div class="row-fluid">
	<div class="span6">
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['edit'], 'class' => 'form-horizontal')); ?>
	</div>

	<div class="span6">
		<?php echo $typeField->input; ?>
		<div id="individualdiv" class="<?php echo $customerType == 'individual' ? '' : 'hide'; ?>">
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['rxindividual'], 'class' => 'form-horizontal')); ?>
			<?php echo $data->form->getControlGroup('address'); ?>
			<div id="appendArea">
			</div>
			<div>
				<div class="btn btn-small btn-info button-add-addr">
					<span class="icon-plus icon-white"></span>
					新增地址
				</div>
			</div>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['office'], 'class' => 'form-horizontal')); ?>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['home'], 'class' => 'form-horizontal')); ?>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['mobile'], 'class' => 'form-horizontal')); ?>
		</div>
		<div id="residentdiv" class="<?php echo $customerType == 'resident' ? '' : 'hide'; ?>">
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['institute'], 'class' => 'form-horizontal')); ?>
		</div>
		<tr>
		<div class="row-fluid js-address-row-tmpl hide ">
			<input class="addr_id hide" type="text" />

			<div class="col-lg-6">
				<?php echo $data->form->getControlGroup('city'); ?>
			</div>
			<div class="col-lg-6">
				<?php echo $data->form->getControlGroup('area'); ?>
			</div>
			<div class="col-lg-11">
				<?php echo $data->form->getControlGroup('address2'); ?>
			</div>
			<button type="button" class="btn btn-default button-delete-addr">
					<span class="glyphicon glyphicon-remove">

					</span>
			</button>
		</div>
		</tr>
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

 		var telOffice = jQuery('#jform_tel_office').val();
		var	telHome = jQuery('#jform_tel_home').val();
		var	telMobile = jQuery('#jform_mobile').val();

		var	jsonTelO = jQuery.parseJSON(telOffice);
		var	jsonTelH = jQuery.parseJSON(telHome);
		var	jsonTelM = jQuery.parseJSON(telMobile);

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
		var birthday = jQuery('#jform_birth_date').val();

		var birthTime = (new Date(birthday)).getTime();

		var now = (new Date()).getTime();

		if(birthday == '' || birthTime >= now){

			var age = '';

			jQuery('#jform_age').val(age);

		} else {

			age = Math.floor((now - birthTime) / 86400 / 365000);

			jQuery('#jform_age').val(age);

		}

		var addrJson = jQuery('#jform_address').val();

		var addr = JSON.parse(addrJson);

		function updateaddr()
		{
			var target = jQuery('#jform_address');
			var data = [];

			handler.remove($(this).closest('div'));

			var addrTmpl = jQuery('.js-address-row');

			addrTmpl.each(function(){
				var arrayToAdd =
				{
					id		: jQuery(this).find('.addr_id').val(),
					city	: jQuery(this).find('.addr_city').val(),
					area	: jQuery(this).find('.addr_area').val(),
					address	: jQuery(this).find('.addr_addr').val()
				};
				data.push(arrayToAdd);
			});

			target.val(JSON.stringify(data));
		}

		function updateadd()
		{
			var target = jQuery('#jform_address');
			var data = [];

			var addrTmpl = jQuery('.js-address-row');

			addrTmpl.each(function(){
				var arrayToAdd =
				{
					id		: jQuery(this).find('.addr_id').val(),
					city	: jQuery(this).find('.addr_city').val(),
					area	: jQuery(this).find('.addr_area').val(),
					address	: jQuery(this).find('.addr_addr').val()
				};
				data.push(arrayToAdd);
			});

			target.val(JSON.stringify(data));
		}


		for(var i = 0; i < addr.length; i++)
		{
			var address = addr[i].address;
			var city 	= addr[i].city;
			var area 	= addr[i].area;
			var id 		= addr[i].id;

			var element = jQuery('.js-address-row-tmpl').clone();
				element.removeClass('js-address-row-tmpl');
				element.removeClass('hide');
				element.addClass('js-address-row');
				element.appendTo('#appendArea');

			element.find('#jform_city').val(city);
			element.find('#jform_area').val(area);
			element.find('#jform_address2').val(address);
			element.find('.addr_id').val(id);


		}

		//Add Addr

		var $panel = $('#appendArea'),
			handler = new MultiRowHandler({$panel:$panel});

		jQuery('.button-add-addr').click(function()
		{
			var target = jQuery('#jform_address');

			$('.js-address-row').chosen();

			$(".chzn-select").val('').trigger("liszt:updated");


			handler.insert($('.js-address-row-tmpl').html());

		});

		// Delete Addr

		jQuery('.button-delete-addr').click(updateaddr);

		jQuery('js-address-row').find('input, select').each( jQuery(this).on('change', updateadd));

	})(jQuery);

</script>

<script>

</script>
