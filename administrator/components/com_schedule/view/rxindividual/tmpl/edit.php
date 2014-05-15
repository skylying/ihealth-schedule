<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Schedule\Helper\ScheduleHelper;

JHtmlBootstrap::tooltip();
JHtmlFormbehavior::chosen('select');
JHtmlBehavior::formvalidation();

$data->asset->addJS('moment-with-langs.min.js');

$basic = $data->form->getFieldset("basic");
$ps    = $data->form->getFieldset("schedules_ps");

$customerID = $data->form->getField('customer_id')->id;
$telOfficeID = $data->form->getField('tel_office')->id;
$telHomeID = $data->form->getField('tel_home')->id;
$mobileID = $data->form->getField('mobile')->id;
$seeDrDateID = $data->form->getField('see_dr_date')->id;
$periodID = $data->form->getField('period')->id;
$createAddressID = $data->form->getField('create_address')->id;

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
var createAddressID = "<?php echo $createAddressID;?>";

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
	 * @param {int} id
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
				$.fn.customerAjax.updateJsonToInputField(telHomeID, tel_home);
				$.fn.customerAjax.updateJsonToInputField(mobileID, mobile);

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
	 * Update customer id_number input value while changing customer_id
	 *
	 * updateCustomerIdNumber
	 *
	 * @param {string} target Target element id
	 * @param {object} id customer_id to update
	 */
	$.fn.customerAjax.updateCustomerIdNumber = function (target, id)
	{
		id = id || "";

		var targetElement = $('#' + target);

		targetElement.val(id);
	};

	/**
	 * Update the hidden input jason file
	 *
	 * updateJsonToInputField
	 *
	 * @param {string} target Target element id
	 * @param {json} dataJson Data to update
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
	 * @param {string} key
	 * @param {json} addressJson
	 */
	$.fn.customerAjax.updateAddressHtml = function (key, addressJson)
	{
		addressJson = addressJson || {};

		// ex: jform_schedule_1st_address
		var targetID = 'jform_schedules_' + key + '_address_id';

		// ex: jform[schedule_1st][address]
		var targetName = 'jform[' + 'schedules_' + key + '][address_id]';

		// Find its parent, later we will replace it with new select list
		var targetsParent = $('#' + targetID).parent();

		var currentSelected = $('#' + targetID).val();

		var html = '';

		// Add select tag
		html += '<select' +
			' name="' + targetName + '"' +
			' id="'   + targetID   + '">';

		for (var i = 0; i < addressJson.length; i++)
		{
			// Add option tag
			html += '<option' +
				' city="' + addressJson[i].city + '"' +
				' area="' + addressJson[i].area + '"' +
				' value="' + addressJson[i].id + '"' +
				((addressJson[i].id == currentSelected) ? 'selected' : '') +
				'>' +
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
	 * @param {int} tagID
	 * @param {json} telJson
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

		html += '<select class="js-select-phone-default pull-left">';

		for (var i = 0; i < defaultLength; i++)
		{
			if (telJson[i] === undefined)
			{
				html += '<option value="' + i + '">' +
					'</option>';
			}
			else
			{
				// Remove whitespace
				telJson[i].number = telJson[i].number.replace(/\s+/g, '');

				// If no numbers has been found, continue.
				if(telJson[i].number == '')
				{
					continue;
				}

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

	$.fn.bindChangeNthScheduleInfo = function ()
	{
		$(this).on('change', function()
		{
			$.fn.toggleNthScheduleInfo(this);
		});
		$(this).toggleNthScheduleInfo();
	};

	$.fn.toggleNthScheduleInfo = function (that)
	{
		that = that || this;
		if ($(that).prop("checked"))
		{
			$(that).closest('.schedules').find('.js-nth-schedule-info').removeClass('opaque');
		}
		else
		{
			$(that).closest('.schedules').find('.js-nth-schedule-info').addClass('opaque');
		}
	};

	/**
	 * Calculate finish drug date, schedule date
	 *
	 * updateScheduleDate
	 *
	 * @param {string} seeDrDate
	 * @param {json} period
	 */
	$.fn.updateScheduleDate = function( seeDrDate, period )
	{
		var moment_date = moment(seeDrDate);

		for (var i = 0; i < addressesKeys.length; i++)
		{
			var drugEmptyDateID = '#jform_schedules_' + addressesKeys[i] + '_drug_empty_date';
			var selectedAddressID = '#jform_schedules_' + addressesKeys[i] + '_address_id';

			// Set finish drug date
			moment_date.add('days', period);
			$(drugEmptyDateID).val( moment_date.format("YYYY-MM-DD") );

			// Get send date
			var addressVal = $(selectedAddressID).val();

			$.ajax({
				type: "POST",
				url: "index.php?option=com_schedule&task=rxindividual.ajax.date" +
					"&address_id=" + addressVal

			}).done(function (cdata)
				{
					var cdata = $.parseJSON(cdata);
					console.log(cdata);
				});
		}

	};
})(jQuery);

jQuery(document).ready(function ()
{
	var phoneDropDown = jQuery('.js-select-phone-default');

	var seeDrDateID = "<?php echo $seeDrDateID;?>";

	var periodID = "<?php echo $periodID;?>";

	// customer_id's element id
	var customerDropDown = jQuery("#" + "<?php echo $customerID;?>");

	// customer_id's value
	var customerID = "<?php echo $customerID;?>";

	// Toggle nth schedules
	jQuery('.js-nth-schedule-check input').each(function(){
		jQuery(this).bindChangeNthScheduleInfo();
	});

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

	jQuery('.js-add-address').on('click', function()
	{
		jQuery(this).closest('.js-nth-schedule-info').find('.js-tmpl-add-addressrow').removeClass('hide');
	});

	jQuery('.js-save-address').on('click', function ()
	{
		// The dynamic row wrapper
		var currentWrap = jQuery(this).closest('.js-tmpl-add-addressrow');

		// The hidden input will save the user customized input address, and wait for model to save.
		var targetHiddenInput = jQuery("#" + createAddressID);

		// Select all the address drop down list, since we have to update all at once
		var targetListToUpdate = jQuery('.js-address-wrap select');

		// Store the concatenated string
		var resultString = '';

		// <option> tag to append
		var html = '';

		// Data to stored
		var data;

		if (targetHiddenInput.val() == '')
		{
			// initialize with array
			data = data || [];
		}
		else
		{
			data = JSON.parse(targetHiddenInput.val());
		}

		var arrayToAdd = {
			id: 'hash-' + data.length,
			city: currentWrap.find('#jform_city').val(),
			area: currentWrap.find('#jform_area').val(),
			address: currentWrap.find('.js-address-row-data').val()
		};

		data.push(arrayToAdd);

		// Concatenate string.
		resultString = currentWrap.find('#jform_city option:selected').text() +
			currentWrap.find('#jform_area option:selected').text() +
			arrayToAdd.address;

		// Form up html <option>
		html = '<option' +
			' city="' + arrayToAdd.city + '"' +
			' area="' + arrayToAdd.area + '"' +
			' value="' + arrayToAdd.id + '">' +
			resultString +
			'</option>';

		// Update drop down list at once
		targetListToUpdate.each(function ()
		{
			jQuery(this).append(html);
			jQuery(this).find('option:last').attr('selected', true);
		});

		// Update to hidden input
		jQuery(this).customerAjax.updateJsonToInputField(createAddressID, data);

		// Clear current row
		currentWrap.addClass('hide');

		// Update Schedule date once
		jQuery(this).updateScheduleDate( jQuery('#'+seeDrDateID).val(), jQuery('#'+periodID).val() );
	});

	jQuery('.js-add-tel').on('click', function ()
	{
		jQuery(this).closest('.js-tel-wrap').find('.js-tmpl-add-telrow').removeClass('hide');
	});

	jQuery('.js-save-tel').on('click', function ()
	{
		var wrapperElement = jQuery(this).closest('.js-tel-wrap');
		var phoneToAdd = wrapperElement.find('.js-tel-row-data');

		// Remove whitespace
		phoneToAdd.val(phoneToAdd.val().replace(/\s+/g, ''));

		if (phoneToAdd != "")
		{
			var data = JSON.parse(wrapperElement.find('input[type=hidden]').val());

			// This value is requirement
			var limit = 3;

			//Only if the data length smaller than limitation will the insertion being executed
			if (data.length < limit)
			{
				var index = 0;
				var b_set = false;

				for (index = 0; index < data.length; index++)
				{
					// Replace the empty field.
					data[index].number = data[index].number.replace(/\s+/g, '');

					// If empty, overwrite it
					if (data[index].number == "")
					{
						data[index].number = phoneToAdd.val();
						data[index].default = 'true';
						b_set = true;

						continue;
					}
					// If not match, reset every element's default to 'false'
					data[index].default = 'false';
				}

				// If no replacement was done, and the length is still not exceed the limit, perform insertion.
				if (!b_set)
				{
					var tagID = wrapperElement.find('input[type=hidden]').prop('id');

					data.push({default: 'true', number: phoneToAdd.val()});

					// Perform html update
					jQuery(this).customerAjax.updatePhoneHtml(tagID, data);

					// Perform hidden input update
					jQuery(this).customerAjax.updateJsonToInputField(tagID, data);
				}
			}
		}

		// Clear the input value
		phoneToAdd.val("");

		// Hide the input row
		jQuery(this).closest('.js-tmpl-add-telrow').addClass('hide');
	});


	jQuery('#'+seeDrDateID).parent().children().each(function(){
		jQuery(this).on('focusout', function(){
			jQuery(this).updateScheduleDate( jQuery('#'+seeDrDateID).val(), jQuery('#'+periodID).val() );
		});
	});

	jQuery('#'+periodID).on('change', function(){
		jQuery(this).updateScheduleDate( jQuery('#'+seeDrDateID).val(), jQuery('#'+periodID).val() );
	});

	jQuery('.js-address-wrap').on('change', '.js-address-list', function(){
		jQuery(this).updateScheduleDate( jQuery('#'+seeDrDateID).val(), jQuery('#'+periodID).val() );
	});

	var $scheduleOne = jQuery('#jform_schedules_1st_deliver_nth0');
	var $scheduleTwo = $scheduleOne.add('#jform_schedules_2nd_deliver_nth0');
	var $scheduleAll = $scheduleTwo.add('#jform_schedules_3rd_deliver_nth0');

	$scheduleAll.on('change', function(){
		jQuery(this).updateScheduleDate( jQuery('#'+seeDrDateID).val(), jQuery('#'+periodID).val() );
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

	input.js-address-row-data
	{
		width: 100%;
	}

	.address label
	{
		display: none;
	}

	input.badge:empty
	{
		display: block !important;
	}
	input.js-tel-row-data
	{
		width: 67%;
		margin-right: 2%;
	}
	.js-select-phone-default
	{
		width: 67%;
		margin-right: 2%;
	}
	.js-add-address
	{
		bottom: -30px;
		position: relative;
	}

	.js-tmpl-add-addressrow{
		padding: 10px 30px;
		margin: 0px -30px;
		background-color: #E2E2E2;
	}

	.opaque
	{
		opacity: .3;
	}
	.custom-well
	{
		margin-bottom: 20px;
		background-color: #f5f5f5;
		border: 1px solid #e3e3e3;
		border-radius: 4px;
		-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
		-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
		box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
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
		<div class="col-lg-7">
			<?php foreach (array("1st", "2nd", "3rd") as $key): ?>
				<?php $schedules = $data->form->getGroup("schedules_{$key}"); ?>
				<div id="schedules_<?php echo $key; ?>" class="row-fluid schedules schedules_<?php echo $key; ?>">
					<div class="col-lg-3 js-nth-schedule-check">
						<?php echo $schedules["jform_schedules_{$key}_deliver_nth"]->getControlGroup(); ?>
					</div>
					<div class="col-lg-9 js-nth-schedule-info custom-well opaque">
						<div class="row-fluid">
							<div class="col-lg-12">
								<div class="row-fluid">
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 js-address-wrap" style="padding: 0px;">
										<?php echo $schedules["jform_schedules_{$key}_address_id"]->getControlGroup(); ?>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px;">
										<div class="btn btn-small btn-info pull-right js-add-address">
											<span class="icon-plus icon-white"></span>
											新增
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-12 js-route-wrap hide">
								<div class="row-fluid">
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px 10px 0px 0px;">
										<?php echo $schedules["jform_schedules_{$key}_sender_id"]->getControlGroup(); ?>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px 10px 0px 0px;">
										<?php echo $schedules["jform_schedules_{$key}_weekday"]->getControlGroup(); ?>
									</div>
								</div>
							</div>
							<!-- Add Address Row -->
							<div class="col-lg-12">
								<div class="js-tmpl-add-addressrow hide">
									<div class="row-fluid">
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding: 0px;">
											<div class="row-fluid">
												<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px 10px 0px 0px;">
													<?php echo $data->form->getInput('city') ?>
												</div>
												<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px 10px 0px 0px;">
													<?php echo $data->form->getInput('area') ?>
												</div>
												<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px">
													<input class="js-address-row-data pull-left" type="text">
												</div>
											</div>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px;">
											<div class="btn btn-small btn-success pull-right js-save-address">
												<span class="icon-ok icon-white"></span>
												儲存
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row-fluid">
							<div class="col-lg-4">
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
				<div class="col-lg-12 js-tel-wrap">
					<div class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('tel_office'); ?>
						</div>
						<!-- This is where to put select list -->
						<div class="controls">
							<input type="text" />
						</div>
						<div class="btn btn-small btn-info pull-left js-add-tel">
							<span class="icon-plus icon-white"></span>
							新增
						</div>
						<input type="hidden" name="<?php echo $telOfficeName; ?>" id="<?php echo $telOfficeID;?>"/>
					</div>
					<!-- Add telephone row -->
					<div class="js-tmpl-add-telrow hide">
						<div class="control-group">
							<div class="controls">
								<input class="js-tel-row-data pull-left" type="text">
							</div>
							<div class="btn btn-small btn-success pull-left js-save-tel">
								<span class="icon-ok icon-white"></span>
								儲存
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12 js-tel-wrap">
					<div class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('tel_home'); ?>
						</div>
						<!-- This is where to put select list -->
						<div class="controls">
							<input type="text" />
						</div>
						<div class="btn btn-small btn-info pull-left js-add-tel">
							<span class="icon-plus icon-white"></span>
							新增
						</div>
						<input type="hidden" name="<?php echo $telHomeName; ?>" id="<?php echo $telHomeID;?>"/>
					</div>
					<!-- Add telephone row -->
					<div class="js-tmpl-add-telrow hide">
						<div class="control-group">
							<div class="controls">
								<input class="js-tel-row-data pull-left" type="text">
							</div>
							<div class="btn btn-small btn-success pull-left js-save-tel">
								<span class="icon-ok icon-white"></span>
								儲存
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12 js-tel-wrap">
					<div class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('mobile'); ?>
						</div>
						<!-- This is where to put select list -->
						<div class="controls">
							<input type="text" />
						</div>
						<div class="btn btn-small btn-info pull-left js-add-tel">
							<span class="icon-plus icon-white"></span>
							新增
						</div>
						<input type="hidden" name="<?php echo $mobileName; ?>" id="<?php echo $mobileID;?>"/>
					</div>
					<!-- Add telephone row -->
					<div class="js-tmpl-add-telrow hide">
						<div class="control-group">
							<div class="controls">
								<input class="js-tel-row-data pull-left" type="text">
							</div>
							<div class="btn btn-small btn-success pull-left js-save-tel">
								<span class="icon-plus icon-white"></span>
								儲存
							</div>
						</div>
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
