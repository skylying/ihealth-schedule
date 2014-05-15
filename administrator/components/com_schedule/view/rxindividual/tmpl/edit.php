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
$timesID = $data->form->getField('times')->id;
$methodID = $data->form->getField('method')->id;

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
var timesID = "<?php echo $timesID;?>";
var seeDrDateID = "<?php echo $seeDrDateID;?>";
var periodID = "<?php echo $periodID;?>";

// Update empty rows of addresses inputs
var addressesKeys = ["1st", "2nd", "3rd"];

(function ($)
{
	/**
	 * This function check if the selector return null
	 *
	 * fireAjax
	 *
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
	 * @param {int} id
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
				var id_number  = cdata.id_number;

				try
				{
					// Update phone numbers
					var tel_office = $.parseJSON(cdata.tel_office);

					// Update phone select list
					$.fn.customerAjax.updatePhoneHtml(telOfficeID, tel_office);
				}
				catch(err)
				{
					$.fn.customerAjax.updatePhoneHtml(telOfficeID);
				}

				try
				{
					// Update phone numbers
					var tel_home = $.parseJSON(cdata.tel_home);

					// Update phone select list
					$.fn.customerAjax.updatePhoneHtml(telHomeID, tel_home);
				}
				catch(err)
				{
					$.fn.customerAjax.updatePhoneHtml(telHomeID);
				}

				try
				{
					// Update phone numbers
					var mobile = $.parseJSON(cdata.mobile);

					// Update phone select list
					$.fn.customerAjax.updatePhoneHtml(mobileID, mobile);
				}
				catch(err)
				{
					$.fn.customerAjax.updatePhoneHtml(mobileID);
				}

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
	 * @param {string} target  Target element id
	 * @param {int}    id      customer_id to update
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
	 * @param {string} target    Target element id
	 * @param {json}   dataJson  Data to update
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
	 * @param {string}  key
	 * @param {json}    addressJson
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

		var addressListClass = 'js-address-list';

		// Add select tag
		html += '<select' +
			' name="'  + targetName + '"' +
			' id="'    + targetID   + '"' +
			' class="' + addressListClass + '">';

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
	 * @param {string}  tagID
	 * @param {json}    telJson
	 */
	$.fn.customerAjax.updatePhoneHtml = function (tagID, telJson)
	{
		telJson = telJson || {};

		var target = $('#' + tagID).parent().find('.controls');
		var defaultLength = telJson.length ? telJson.length : 0;

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
		var data;

		if (hiddenInput.val() == '' || hiddenInput.val() == '{}')
		{
			// initialize with array
			data = [];
		}
		else
		{
			// initialize with input
			data = JSON.parse(hiddenInput.val());
		}

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

	/**
	 * Bind change schedules' cheboxes
	 *
	 * bindChangeNthScheduleInfo
	 *
	 * return void
	 */
	$.fn.bindChangeNthScheduleInfo = function ()
	{
		$(this).on('change', function()
		{
			$.fn.toggleNthScheduleInfo(this);

		});
		$(this).toggleNthScheduleInfo();
	};

	/**
	 * Bind schedules' checkboxes on 'toggle' opaque effect and 'update sender date' method
	 *
	 * toggleNthScheduleInfo
	 *
	 * return void
	 */
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
	 * @param {string}    seeDrDate
	 * @param {json}      period
	 */
	$.fn.updateScheduleDate = function (seeDrDate, period)
	{
		var moment_date = moment(seeDrDate);

		for (var i = 0; i < addressesKeys.length; i++)
		{
			var drugEmptyDateID = '#jform_schedules_' + addressesKeys[i] + '_drug_empty_date';
			var selectedAddressID = '#jform_schedules_' + addressesKeys[i] + '_address_id';
			var deliveredNth = '#jform_schedules_' + addressesKeys[i] + '_deliver_nth0';

			// Set finish drug date
			moment_date.add('days', period);
			$(drugEmptyDateID).val(moment_date.format("YYYY-MM-DD"));

			if ($(deliveredNth).is(":checked"))
			{
				var address = $(selectedAddressID).val();
				var city = $(selectedAddressID).find('option:selected').attr('city');
				var area = $(selectedAddressID).find('option:selected').attr('area');

				$.ajax({
					type: "POST",
					url: "index.php?option=com_schedule&task=rxindividual.ajax.date" +
						"&nth=" + addressesKeys[i] +
						"&city_id=" + city +
						"&area_id=" + area +
						"&see_dr_date=" + seeDrDate +
						"&period=" + period
				}).done(function (cdata)
					{
						var data = JSON.parse(cdata);

						var sendDateId = '#jform_schedules_' + data['nth'] + '_date';

						if (data['date'] != null)
						{
							$(sendDateId).closest('.js-nth-schedule-info').find('.js-route-wrap').addClass('hide');

							$(sendDateId).val(data['date']);
						}
						else
						{
							if (data['type'] == '2')
							{
								$(sendDateId).closest('.js-nth-schedule-info').find('.js-route-wrap').removeClass('hide');

								$(sendDateId).val('');
								Joomla.renderMessages([
									[data['message']]
								]);
							}
						}
					});
			}
		}
	};

	/**
	 * Show and hide schedules edit box by changing send drug times
	 *
	 * updateScupdateSchedulesEditBoxheduleDate
	 *
	 * return void
	 *
	 */
	$.fn.showSchedulesEditBlock = function ()
	{
		var schedules1 = $('.schedules').eq(0);
		var schedules2 = $('.schedules').eq(1);
		var schedules3 = $('.schedules').eq(2);

		var checkbox1 = schedules1.find('.js-nth-schedule-check input');
		var checkbox2 = schedules2.find('.js-nth-schedule-check input');
		var checkbox3 = schedules3.find('.js-nth-schedule-check input');

		switch($(this).val()){
			case '1':
				// Check 1
				checkbox1.attr('checked', true).trigger('change');
				checkbox2.attr('checked', false).trigger('change');
				checkbox3.attr('checked', false).trigger('change');
				// Show 1
				schedules1.removeClass('hide');
				schedules2.addClass('hide');
				schedules3.addClass('hide');
				break;
			case '2':
				// Check 2
				checkbox1.attr('checked', false).trigger('change');
				checkbox2.attr('checked', true).trigger('change');
				checkbox3.attr('checked', false).trigger('change');
				// Show 1, 2
				schedules1.removeClass('hide');
				schedules2.removeClass('hide');
				schedules3.addClass('hide');
				break;
			case '3':
				// Check 2,3
				checkbox1.attr('checked', false).trigger('change');
				checkbox2.attr('checked', true).trigger('change');
				checkbox3.attr('checked', true).trigger('change');
				// Show all
				schedules1.removeClass('hide');
				schedules2.removeClass('hide');
				schedules3.removeClass('hide');
				break;
			default:
				break;
		}
	};

	$.fn.methodForm = function ()
	{
		return this.each(function ()
		{
			// Find where to store hicodes
			var targetHiddenInput = $('#jform_hicodes_json');

			// Update while hicode date already exist on the very first time
			if (targetHiddenInput.val() != '' && targetHiddenInput.val() != '{}')
			{
				$.fn.methodForm.insertHicodeTableRow(JSON.parse(targetHiddenInput.val()));
			}

			// Copy the HiCode template.
			var tableTmpl = $('.js-hicode-tmpl');

			// Append tabel after method select list
			tableTmpl.insertAfter($(this).closest('.control-group'));

			// Bind Prescription method change detection
			$(this).on('change', function ()
			{
				if ($(this).val() == 'form')
				{
					// Update from input
					$.fn.methodForm.updateHicodeHiddenInput();
					// Show table
					tableTmpl.removeClass('hide');
				}
				else
				{
					var targetHiddenInput = $('#jform_hicodes_json');
					targetHiddenInput.val('');
					tableTmpl.addClass('hide');
				}
			});

			// Trigger once
			$(this).trigger('change');

			// Combine two selector.
			var hicodeElement = $('.js-hicode-code');
			var quantityElement = $('.js-hicode-quantity');
			var combinedHicodeElem = hicodeElement.add(quantityElement);

			// Every time when 'hicode' and 'quantity' being changed.
			tableTmpl.on('change', combinedHicodeElem, function ()
			{
				$.fn.methodForm.updateHicodeHiddenInput();
			});

			// Bind Add event
			$('.js-hicode-add-row').on('click', function ()
			{
				var cloneRow = $(".js-hicode-row").first().clone();
				// Retrieve hicode
				cloneRow.find('.js-hicode-code').val('');
				// Retrieve quantity
				cloneRow.find('.js-hicode-quantity').val('');
				// Retrieve id
				cloneRow.find('.js-hicode-id').val('');

				$('.js-hicode-tmpl tbody').append(cloneRow);
			});

			// Bind Delete event
			$('.js-hicode-tmpl').on('click', '.js-hicode-delete-row',function ()
			{
				if (confirm('您確定要刪除嗎？'))
				{
					if ($(".js-hicode-row").size() > 1)
					{
						// Delete row
						$(this).closest('.js-hicode-row').remove();
						// Update Hidden Input
						$.fn.methodForm.updateHicodeHiddenInput();
					}
					else
					{
						var row = $(".js-hicode-row").first();
						// Retrieve hicode
						row.find('.js-hicode-code').val('');
						// Retrieve quantity
						row.find('.js-hicode-quantity').val('');
						// Retrieve id
						row.find('.js-hicode-id').val('');

						// Update Hidden Input
						$.fn.methodForm.updateHicodeHiddenInput();
					}
				}
			});
		});
	};

	$.fn.methodForm.updateHicodeHiddenInput = function ()
	{
		var newRowCounter = 0;
		var totalRowCounter = 0;

		// Data to stored
		var data = [];

		// Go through every row, and push it into hidden input
		$('.js-hicode-row').each(function ()
		{
			// Retrieve hicode
			var code = $(this).find('.js-hicode-code').val();
			// Retrieve quantity
			var quantity = $(this).find('.js-hicode-quantity').val();
			// Retrieve id
			var id = $(this).find('.js-hicode-id').val();

			// Check if hash Exist
			if (id.indexOf("hash-") > -1)
			{
				// indexOf returns the position of the string in the other string.
				// If not found, it will return -1:

				// Make sure every info is provided
				if ((code != '') && (quantity != ''))
				{
					data.push({id: 'hash-' + newRowCounter, hicode: code, quantity: quantity});
					newRowCounter++;
					totalRowCounter++;
				}
			}
			// if hash- doesn't exist, and id doesn't exist => blank new row
			else if (id.indexOf("hash-") == -1 && id == '')
			{
				// Make sure every info is provided
				if ((code != '') && (quantity != ''))
				{
					data.push({id: 'hash-' + newRowCounter, hicode: code, quantity: quantity});
					newRowCounter++;
					totalRowCounter++;
				}
			}
			// if hash- doesn't exist, and id exist
			else if ((id.indexOf("hash-") == -1) && (id != ''))
			{
				// Make sure every info is provided
				if ((code != '') && (quantity != ''))
				{
					data.push({id: id, hicode: code, quantity: quantity});
					totalRowCounter++;
				}
			}

			// Perform hidden input update
			$(this).customerAjax.updateJsonToInputField('jform_hicodes_json', data);

			// Update Counter
			$('.js-hicode-amount').text(totalRowCounter);
		});
	};

	$.fn.methodForm.insertHicodeTableRow = function (data)
	{
		var tableTbody = $('.js-hicode-tmpl tbody');
		var cloneRow = $(".js-hicode-row").first().clone();

		// Retrieve hicode
		cloneRow.find('.js-hicode-code').val('');
		// Retrieve quantity
		cloneRow.find('.js-hicode-quantity').val('');
		// Retrieve id
		cloneRow.find('.js-hicode-id').val('');

		// Clear tbody
		tableTbody.html('');

		for (var i = 0; i < data.length; i++)
		{
			var insertRow = cloneRow.clone();

			insertRow.find('.js-hicode-code').val(data[i].hicode);
			// Retrieve quantity
			insertRow.find('.js-hicode-quantity').val(data[i].quantity);
			// Retrieve id
			insertRow.find('.js-hicode-id').val(data[i].id);

			tableTbody.append(insertRow);
		}

		if (data.length != 0)
		{
			tableTbody.append(cloneRow);
		}
	};

})(jQuery);

jQuery(document).ready(function ()
{
	var phoneDropDown = jQuery('.js-select-phone-default');

	var seeDrDateID = "<?php echo $seeDrDateID;?>";

	var periodID = "<?php echo $periodID;?>";

	var methodID = "<?php echo $methodID;?>";

	// customer_id's element id
	var customerDropDown = jQuery("#" + "<?php echo $customerID;?>");

	// customer_id's value
	var customerID = "<?php echo $customerID;?>";

	// Toggle nth schedules
	jQuery('.js-nth-schedule-check input').each(function ()
	{
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

	// Bind add new address
	jQuery('.js-add-address').on('click', function ()
	{
		// Find the address template row
		var element = jQuery('.js-tmpl-add-addressrow').removeClass('hide');

		// Insert to target position, one should know that only one '.js-tmpl-add-addressrow' exist since appendTo method
		element.appendTo(jQuery(this).closest('.js-nth-schedule-info').find('.js-add-address-position'));
	});

	// Bind save new address
	jQuery('.js-nth-schedule-info').on('click', '.js-save-address',function ()
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

		if (targetHiddenInput.val() == '' || targetHiddenInput.val() == '{}')
		{
			// initialize with array
			data = [];
		}
		else
		{
			// initialize with input
			data = JSON.parse(targetHiddenInput.val());
		}

		var objectToAdd = {
			id: 'hash-' + data.length,
			city: currentWrap.find('#jform_city').val(),
			area: currentWrap.find('#jform_area').val(),
			address: currentWrap.find('.js-address-row-data').val()
		};

		if ((objectToAdd.city == '')
			|| (objectToAdd.area == '')
			|| (objectToAdd.address == ''))
		{
			// Notify user to make sure they input correctly
			Joomla.renderMessages([['欄位輸入不完整']]);
		}
		else if ((objectToAdd.city != '')
			|| (objectToAdd.area != '')
			|| (objectToAdd.address != ''))
		{
			data.push(objectToAdd);

			// Concatenate string.
			resultString = currentWrap.find('#jform_city option:selected').text() +
				currentWrap.find('#jform_area option:selected').text() +
				objectToAdd.address;

			// Form up html <option>
			html = '<option' +
				' city="' + objectToAdd.city + '"' +
				' area="' + objectToAdd.area + '"' +
				' value="' + objectToAdd.id + '">' +
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
			jQuery(this).updateScheduleDate(jQuery('#' + seeDrDateID).val(), jQuery('#' + periodID).val());
		}
	});

	// Bind add new telephone
	jQuery('.js-add-tel').on('click', function ()
	{
		jQuery(this).closest('.js-tel-wrap').find('.js-tmpl-add-telrow').removeClass('hide');
	});

	// Bind save new telephone
	jQuery('.js-save-tel').on('click', function ()
	{
		var wrapperElement = jQuery(this).closest('.js-tel-wrap');
		var phoneToAdd = wrapperElement.find('.js-tel-row-data');

		// Remove whitespace
		phoneToAdd.val(phoneToAdd.val().replace(/\s+/g, ''));

		if (phoneToAdd != "")
		{
			// This value is a requirement
			var limit = 3;

			var b_set = false;

			var data;

			var inputValue = wrapperElement.find('input[type=hidden]').val();

			if (inputValue == '' || inputValue == '{}')
			{
				// initialize with array
				data = [];
			}
			else
			{
				// initialize with input
				data = JSON.parse(inputValue);
			}

			//Only if the data length smaller than limitation will the insertion being executed
			if (data.length < limit)
			{
				for (var index = 0; index < data.length; index++)
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

	// Bind See Doctor Date
	jQuery('#' + seeDrDateID).parent().children().each(function ()
	{
		jQuery(this).on('focusout', function ()
		{
			jQuery(this).updateScheduleDate(jQuery('#' + seeDrDateID).val(), jQuery('#' + periodID).val());
		});
	});

	jQuery('#' + periodID).on('change', function ()
	{
		jQuery(this).updateScheduleDate(jQuery('#' + seeDrDateID).val(), jQuery('#' + periodID).val());
	});

	jQuery('.js-address-wrap').on('change', '.js-address-list', function ()
	{
		jQuery(this).updateScheduleDate(jQuery('#' + seeDrDateID).val(), jQuery('#' + periodID).val());
	});

	// Combine selector, whenever schedule's checkboxes are changed, fire get sender date update.
	var $scheduleOne = jQuery('#jform_schedules_1st_deliver_nth0');
	var $scheduleTwo = $scheduleOne.add('#jform_schedules_2nd_deliver_nth0');
	var $scheduleAll = $scheduleTwo.add('#jform_schedules_3rd_deliver_nth0');

	$scheduleAll.on('change', function ()
	{
		jQuery(this).updateScheduleDate(jQuery('#' + seeDrDateID).val(), jQuery('#' + periodID).val());
	});

	// simultaneously show and hide schedules
	jQuery('#' + timesID).on('change', function ()
	{
		jQuery(this).showSchedulesEditBlock();
	});

	// show and hide schedules once on load
	jQuery('#' + timesID).showSchedulesEditBlock();

	// Bind Drug Period
	jQuery('#' + periodID).on('change', function ()
	{
		jQuery(this).updateScheduleDate(jQuery('#' + seeDrDateID).val(), jQuery('#' + periodID).val());
	});

	// Method list
	jQuery('#' + methodID).methodForm();
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
		<div class="col-lg-5 col-md-5 col-sm-12">
			<?php
			foreach ($basic as $field)
			{
				echo $field->getControlGroup();
			}
			?>
		</div>
		<div class="col-lg-7 col-md-7 col-sm-12">
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
							<div class="col-lg-12 js-add-address-position">

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

	<!-- HICODE TEMPLATE-->
	<div class="control-group custom-well js-hicode-tmpl hide">
		<table class="table table-striped">
			<thead>
			<tr>
				<th class="text-center">健保碼</th>
				<th class="text-center">總量</th>
				<th class="text-center">刪除</th>
				<th class="text-center">總數小計</th>
			</tr>
			</thead>

			<tfoot>
			<td colspan="3">
				<a class="btn btn-default btn-success js-hicode-add-row">
					<i class="glyphicon glyphicon-plus"></i>
					新增欄位
				</a>
			</td>
			<td colspan="1">
				<p class="text-center"><span class="js-hicode-amount" style="font-size: 2.5rem;"></span></p>
			</td>
			</tfoot>

			<tbody>
			<tr class="js-hicode-row">
				<td>
					<input class="js-hicode-code" style="width:100%;" type="text">
				</td>
				<td>
					<input class="js-hicode-quantity" style="width:100%;" type="text">
				</td>
				<td>
					<button type="button" class="btn btn-default btn-sm js-hicode-delete-row">
						<span class="glyphicon glyphicon-trash"></span>
					</button>
				</td>
				<td>
					<input class="js-hicode-id" style="width:100%;" type="hidden">
				</td>
			</tr>
			</tbody>
		</table>
	</div>

	<!-- ADD ADDRESS ROW TEMPLATE -->
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

	<!-- This hidden input only for temperately testing-->
	<input type="hidden" id="jform_hicodes_json"/>

	<div>
		<input type="hidden" name="option" value="com_schedule" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
