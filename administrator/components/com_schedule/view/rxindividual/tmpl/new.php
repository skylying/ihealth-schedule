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

$basic = $data->form->getFieldset("basic");
$ps    = $data->form->getFieldset("schedules_ps");

$customerID = $data->form->getField('customer_id')->id;
$telOfficeID = $data->form->getField('tel_office')->id;
$telHomeID = $data->form->getField('tel_home')->id;
$mobileID = $data->form->getField('mobile')->id;

$telOfficeName = $data->form->getField('tel_office')->name;
$telHomeName   = $data->form->getField('tel_home')->name;
$mobileName    = $data->form->getField('mobile')->name;

$customerIDNumber = $data->form->getField('id_number')->id;

?>

<script type="text/javascript">
var telOfficeID = "<?php echo $telOfficeID;?>";
var telHomeID = "<?php echo $telHomeID;?>";
var mobileID = "<?php echo $mobileID;?>";
var customerIDNumber = "<?php echo $customerIDNumber;?>";

// Update empty rows of addresses inputs
var addressesKeys = ["1st", "2nd", "3rd"];

(function ($)
{
	/**
	 * This function check if the selector return null
	 *
	 * fireAjax
	 *
	 * @param id
	 */
	$.fn.exists = function ()
	{
		return this.length !== 0;
	};

	/**
	 * Entering point of plugin customerAjax
	 *
	 * customerAjax
	 *
	 * @param id
	 */
	$.fn.customerAjax = function (id)
	{
		id = id || this.val();
		return this.each(function ()
		{
			$.fn.customerAjax.fireAjax(id);
		});
	};

	/**
	 * Fire ajax request and get from Customer model and Addresses modl
	 *
	 * fireAjax
	 *
	 * @param id
	 */
	$.fn.customerAjax.fireAjax = function (id)
	{
		// Fire ajax to Customer
		$.ajax({
			type: "POST",
			url: "index.php?option=com_schedule&task=customer.ajax.json&id=" + id
		}).done(function (cdata)
			{
				var cdata      = $.parseJSON(cdata);
				var tel_office = $.parseJSON(cdata.tel_office);
				var tel_home   = $.parseJSON(cdata.tel_home);
				var mobile     = $.parseJSON(cdata.mobile);
				var id_number  = cdata.id_number;

				// Update phone numbers
				$.fn.customerAjax.updatePhoneHtml(telOfficeID, tel_office);
				$.fn.customerAjax.updatePhoneHtml(telHomeID, tel_home);
				$.fn.customerAjax.updatePhoneHtml(mobileID, mobile);

				// Update hidden input which store phone number json string.
				$.fn.customerAjax.updateJsonToInputField(telOfficeID, tel_office);
				$.fn.customerAjax.updateJsonToInputField(telHomeID, tel_office);
				$.fn.customerAjax.updateJsonToInputField(mobileID, tel_office);

				// Update customer id_number
				$.fn.customerAjax.updateCustomerIdNumber(customerIDNumber, id_number);
			});

		//Fire ajax to Addresses
		$.ajax({
			type: "POST",
			url: "index.php?option=com_schedule&task=addresses.ajax.json&id=" + id
		}).done(function (cdata)
			{
				var cdata = $.parseJSON(cdata);

				// Update empty rows of addresses inputs
				for (var i = 0; i < addressesKeys.length; i++)
				{
					$.fn.customerAjax.updateAddressHtml(addressesKeys[i], cdata);
				}
			});
	};

	/**
	 * Upadate customer id_number input value while change customer_id
	 *
	 * updateCustomerIdNumber
	 *
	 * @param target Target element id
	 * @param data Data to update
	 */
	$.fn.customerAjax.updateCustomerIdNumber = function (target, data)
	{
		data = data || {};

		var targetElement = $('#' + target);

		targetElement.val(data);
	};

	/**
	 * Update the hidden input jason file
	 *
	 * updateJsonToInputField
	 *
	 * @param target Target element id
	 * @param dataJson Data to update
	 */
	$.fn.customerAjax.updateJsonToInputField = function (target, dataJson)
	{
		dataJson = dataJson || {};

		var targetElement = $('#' + target);

		// Check if selector get null
		if (targetElement.exists())
		{
			targetElement.val(JSON.stringify(dataJson));
		}
	};

	/**
	 * Update address select list row
	 *
	 * updateAddressHtml
	 *
	 * @param key
	 * @param addressJson
	 */
	$.fn.customerAjax.updateAddressHtml = function (key, addressJson)
	{
		addressJson = addressJson || {};

		// ex: jform_schedule_1st_address
		var targetID = 'jform_schedules_' + key + '_address_id';

		// ex: jform[schedule_1st][address]
		var targetName = 'jform[' + 'schedule_' + key + '][address_id]';

		// Find its parent, later we will replace it with new select list
		var targetsParent = $('#' + targetID).parent();

		var html = '';

		// Add select tag
		html += '<select' +
			' name="' + targetName + '"' +
			' id="'   + targetID   + '">';

		for (var i = 0; i < addressJson.length; i++)
		{
			// Add option tag
			html += '<option' +
				' val="' + addressJson[i].id + '">' +
				addressJson[i].city_title +
				addressJson[i].area_title +
				addressJson[i].address +
				'</option>';
		}

		html += '</select>';

		//Clear target hook's html first.
		targetsParent.html("");

		targetsParent.html(html);
	};

	/**
	 * Update phone input list row
	 *
	 * updatePhoneHtml
	 *
	 * @param tagID
	 * @param telJson
	 */
	$.fn.customerAjax.updatePhoneHtml = function (tagID, telJson)
	{
		telJson = telJson || {};

		var target = $('#' + tagID).parent().find('.controls');
		var defaultLength = telJson.length ? telJson.length : 3;

		//Clear target hook's html first.
		target.html("");

		//Update rows, append new input rows to target element
		var html = '';

		html += '<select class="js-select-phone-default">';

		for (var i = 0; i < defaultLength; i++)
		{
			if (telJson[i] === undefined)
			{
				html += '<option value="' + i + '">' +
					'</option>';
			}
			else
			{
				html += '<option value="' + i + '" ' +
					((telJson[i].default == 'true') ? 'selected' : '') + '>' +
					(telJson[i].number ? telJson[i].number : '') +
					'</option>';
			}
		}
		html += '</select>';
		target.append(html);
	};

	/**
	 * Every time user select different phone number, the default will be overwritten
	 *
	 * updateHiddenPhoneNumbersInput
	 *
	 * return void
	 */
	$.fn.updateHiddenPhoneNumbersInput = function ()
	{
		var key = this.find('option:selected').val();
		var hiddenInput = this.closest('.control-group').find('input');

		// Parse input string into object
		var data = JSON.parse(hiddenInput.val());

		for (var i = 0; i < data.length; i++)
		{
			data[i].default = 'false';

			// Set the selected option to true
			if (i == key)
			{
				data[i].default = 'true';
			}
		}
		hiddenInput.val(JSON.stringify(data));
	};
})(jQuery);

jQuery(document).ready(function ()
{
	var phoneDropDown = jQuery('.js-select-phone-default');

	// customer_id's element id
	var customerDropDown = jQuery("#" + "<?php echo $customerID;?>");

	// customer_id's value
	var customerID = "<?php echo $customerID;?>";

	console.log(customerDropDown.val());
	// If customer id is not set, select the first option, and update once on load

	customerDropDown.customerAjax(customerDropDown.val());

	// Fire ajax request everytime customer_id has been changed
	customerDropDown.on('change', function ()
	{
		jQuery(this).customerAjax();
	});

	// Every time user select different phone number, the default will be overwritten
	jQuery('form').on('change', '.js-select-phone-default', function ()
	{
		jQuery(this).updateHiddenPhoneNumbersInput();
	});
});
</script>

<style>
	.schedules .control-label
	{
		float: none;
	}

	.schedules .controls
	{
		margin-left: 0;
	}

	.schedules input
	{
		width: 80%;
	}

	.schedules select
	{
		width: 100%;
	}

	.address label
	{
		display: none;
	}
</style>

<form name="adminForm" id="adminForm" method="post" action="<?php echo JURI::getInstance(); ?>" class="form-horizontal"
	enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="col-lg-5">
			<?php
			foreach ($basic as $field)
			{
				echo $field->getControlGroup();
			}
			?>
		</div>
		<div class="col-lg-5 col-lg-offset-1">
			<?php foreach (array("1st", "2nd", "3rd") as $key): ?>
				<?php $schedules = $data->form->getGroup("schedules_{$key}"); ?>
				<div id="schedules_<?php echo $key; ?>" class="row-fluid schedules schedules_<?php echo $key; ?>">
					<div class="col-lg-3">
						<!-- TODO: 換成可愛的圓圈圈 -->
						<?php echo $schedules["jform_schedules_{$key}_deliver_nth"]->getControlGroup(); ?>
					</div>
					<div class="col-lg-9">
						<div class="row-fluid address">
							<div class="col-lg-12">
								<?php echo $schedules["jform_schedules_{$key}_address_id"]->getControlGroup(); ?>
							</div>
						</div>
						<div class="row-fluid">
							<div class="col-lg-4">
								<!-- TODO:js -->
								<?php echo $schedules["jform_schedules_{$key}_drug_empty_date"]->getControlGroup(); ?>
							</div>
							<div class="col-lg-4">
								<?php echo $schedules["jform_schedules_{$key}_date"]->getControlGroup(); ?>
							</div>
							<div class="col-lg-4">
								<?php echo $schedules["jform_schedules_{$key}_session"]->getControlGroup(); ?>
							</div>
						</div>
					</div>

					<?php echo $schedules["jform_schedules_{$key}_schedule_id"]->getControlGroup(); ?>
                </div>
			<?php endforeach; ?>
			<div class="row-fluid well">
				<div class="col-lg-12">
					<div class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('tel_office'); ?>
						</div>
						<div class="controls">
							<input type="text" />
						</div>
						<input type="hidden" name="<?php echo $telOfficeName; ?>" id="<?php echo $telOfficeID;?>"/>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('tel_home'); ?>
						</div>
						<div class="controls">
							<input type="text" />
						</div>
						<input type="hidden" name="<?php echo $telHomeName; ?>" id="<?php echo $telHomeID;?>"/>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('mobile'); ?>
						</div>
						<div class="controls">
							<input type="text" />
						</div>
						<input type="hidden" name="<?php echo $mobileName; ?>" id="<?php echo $mobileID;?>"/>
					</div>
				</div>
				<div class="col-lg-12">
					<?php echo $data->form->getControlGroup('note'); ?>
					<?php echo $data->form->getControlGroup('note_list'); ?>
				</div>
			</div>
		</div>
	</div>

	<div>
		<input type="hidden" name="option" value="com_schedule" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
