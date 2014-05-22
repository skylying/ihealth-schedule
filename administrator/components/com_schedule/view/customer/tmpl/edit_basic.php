<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$tab = $data->tab;
$fieldsets = $data->form->getFieldsets();
$typeField = $data->form->getField('type');
$customerType = $data->item->type;
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
			<div id="appendArea"></div>
			<div class="btn btn-md btn-info button-add-addr">
				<span class="icon-plus icon-white"></span>
				新增地址
			</div>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['office'], 'class' => 'form-horizontal')); ?>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['home'], 'class' => 'form-horizontal')); ?>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['mobile'], 'class' => 'form-horizontal')); ?>
		</div>
		<div id="residentdiv" class="<?php echo $customerType == 'resident' ? '' : 'hide'; ?>">
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['institute'], 'class' => 'form-horizontal')); ?>
		</div>
	</div>
</div>

<div class="row-fluid js-address-row-tmpl hide">
	<div class="row-fluid alert alert-success">
		<input class="addr_id hide" type="text" />

		<div class="col-lg-2 panel-info">
			<input type="radio" name="previous" class="previous" />
			主要
		</div>
		<div class="col-lg-5">
			<?php echo $data->form->getControlGroup('city'); ?>
		</div>
		<div class="col-lg-5">
			<?php echo $data->form->getControlGroup('area'); ?>
		</div>
		<div class="col-lg-12">
			<?php echo $data->form->getControlGroup('address2'); ?>
		</div>
		<button type="button" class="btn btn-default button-delete-addr pull right">
			<span class="glyphicon glyphicon-remove"></span>
		</button>
	</div>
</div>

<script type="text/javascript">
(function ($)
{
	$('.customertype input').on('click', function ()
	{
		var individualDiv = $('#individualdiv'),
			residentDiv = $('#residentdiv');

		if ($(this).val() == 'individual')
		{
			alert('您確定要更換身份?，住民資料將不會儲存。');
			individualDiv.removeClass('hide');
			residentDiv.addClass('hide');
		}
		else
		{
			alert('您確定要更換身份?，散客資料將不會儲存。');
			individualDiv.addClass('hide');
			residentDiv.removeClass('hide');
		}
	});

	// Get telephone jsonDatas
	var jsonTelO = $.parseJSON($('#jform_tel_office').val());
	var jsonTelH = $.parseJSON($('#jform_tel_home').val());
	var jsonTelM = $.parseJSON($('#jform_mobile').val());

	/**
	 * 複寫Json回電話欄位
	 */
	function update()
	{
		jsonTelO = [
			{ "default": $('#radiojform_tel_office0').is(':checked'), "number": $('#jform_tel_office0').val()},
			{ "default": $('#radiojform_tel_office1').is(':checked'), "number": $('#jform_tel_office1').val()},
			{ "default": $('#radiojform_tel_office2').is(':checked'), "number": $('#jform_tel_office2').val()}
		];

		jsonTelH = [
			{ "default": $('#radiojform_tel_home0').is(':checked'), "number": $('#jform_tel_home0').val()},
			{ "default": $('#radiojform_tel_home1').is(':checked'), "number": $('#jform_tel_home1').val()},
			{ "default": $('#radiojform_tel_home2').is(':checked'), "number": $('#jform_tel_home2').val()}
		];

		jsonTelM = [
			{ "default": $('#radiojform_mobile0').is(':checked'), "number": $('#jform_mobile0').val()},
			{ "default": $('#radiojform_mobile1').is(':checked'), "number": $('#jform_mobile1').val()},
			{ "default": $('#radiojform_mobile2').is(':checked'), "number": $('#jform_mobile2').val()}
		];

		$('#jform_tel_office').val(JSON.stringify(jsonTelO));
		$('#jform_tel_home').val(JSON.stringify(jsonTelH));
		$('#jform_mobile').val(JSON.stringify(jsonTelM));
	}

	$('#customer-edit-fieldset-office, ' + '#customer-edit-fieldset-home, ' + '#customer-edit-fieldset-mobile input').each(function ()
	{
		$(this).on('change', update);
	});

	//calculate age
	$('#jform_birth_date').on('dp.change', function ()
	{

		var birthday = $('#jform_birth_date').val();

		var birthTime = (new Date(birthday)).getTime();

		var now = (new Date()).getTime();

		if (birthday == '' || birthTime >= now)
		{
			$('#jform_age').val();
		}
		else
		{
			var age = Math.floor((now - birthTime) / 86400 / 365000);

			$('#jform_age').val(age);
		}
	});

	/**
	 * Delete addresses function
	 */
	function deleteAddr()
	{
		var target = $('#jform_address');
		var addrTmpl = $('.js-address-row');
		var data = [];
		var $panel = $('#appendArea'),
			handler = new MultiRowHandler({$panel: $panel});

		handler.remove($(this).closest('.js-address-row'));

		addrTmpl.each(function ()
		{
			var ifChecked = $(this).find('.previous').prop('checked') ? '1' : '0';

			var objectToAdd =
			{
				id: $(this).find('.addr_id').val(),
				city: $(this).find('.addr_city').val(),
				area: $(this).find('.addr_area').val(),
				address: $(this).find('.addr_addr').val(),
				previous: ifChecked
			};

			data.push(objectToAdd);
		});

		target.val(JSON.stringify(data));
	}

	/**
	 * Update addresses function
	 */
	function updateAddr()
	{
		var target = $('#jform_address');
		var data = [];
		var addrTmpl = $('.js-address-row');

		//Get new address data
		addrTmpl.each(function ()
		{
			var ifChecked = $(this).find('.previous').prop('checked') ? '1' : '0';
			var objectToAdd =
			{
				id: $(this).find('.addr_id').val(),
				city: $(this).find('.addr_city').val(),
				area: $(this).find('.addr_area').val(),
				address: $(this).find('.addr_addr').val(),
				previous: ifChecked
			};

			//Push json data back
			data.push(objectToAdd);
		});

		target.val(JSON.stringify(data));
	}

	//Parse地址Json欄位
	var addrJson = $('#jform_address').val();

	var addr = JSON.parse(addrJson);

	//塞資料到每個新的tmpl
	for (var i = 0; i < addr.length; i++)
	{
		var address = addr[i].address;
		var city = addr[i].city;
		var area = addr[i].area;
		var id = addr[i].id;
		var previous = addr[i].previous;

		//Clone tmpl
		var element = $('.js-address-row-tmpl').clone();
		element.removeClass('js-address-row-tmpl hide');
		element.addClass('js-address-row');
		element.appendTo('#appendArea');

		//Insert data into all the columns
		element.find('#jform_city').val(city);
		element.find('#jform_area').val(area);
		element.find('#jform_address2').val(address);
		element.find('.addr_id').val(id);

		//Check radiobox
		if (previous == '1')
		{
			element.find('.previous').prop('checked', true);
		}
	}

	/**
	 * Add new address button
	 */
	$('.button-add-addr').click(function ()
	{
		var element = $('.js-address-row-tmpl').clone();
		element.removeClass('js-address-row-tmpl hide');
		element.addClass('js-address-row');
		element.appendTo('#appendArea');

		$('#appendArea select').chosen();

		//Close second descendant div from select
		$('#appendArea select').next().next().remove();
	});

	// Delete addresses
	$('#appendArea').on('click', '.button-delete-addr', deleteAddr);

	// Update addresses
	$('#appendArea').on('change', 'select[name="jform[city]"]', updateAddr);
	$('#appendArea').on('change', 'select[name="jform[area]"]', updateAddr);
	$('#appendArea').on('change', 'input[name="jform[address2]"]', updateAddr);

})(jQuery);
</script>
