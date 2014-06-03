;
(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console.log */

	if (window.CustomerFieldHandler !== undefined)
	{
		return;
	}

	// Exports class CustomerFieldHandler
	window.CustomerFieldHandler = {
		setOptions: function(options)
		{
			// Overwrite with user's options
			this.options = $.extend(true, {
				customerId           : null,
				customerIdNumber     : null,
				telOfficeId          : null,
				telHomeId            : null,
				mobileId             : null,
				addressesKeys        : ["1st", "2nd", "3rd"],
				createAddressId      : null,
				seeDrDateId          : null,
				periodId             : null,
				hospitalId           : null,
				birthDateId          : null
			}, options);
		},

		run: function()
		{
			var self = this;
			var customerDropDown = jQuery("#" + this.options.customerId);
			// Fire update onload.
			this.fireAjax(customerDropDown.val());

			// Fire ajax request while using modal to add new customer
			customerDropDown.on('liszt:updated', function()
			{
				self.fireAjax(customerDropDown.val());
			});

			// Fire ajax request every time customer_id has been changed
			customerDropDown.on('change', function()
			{
				self.fireAjax(customerDropDown.val());
			});

			// Every time user select different phone number, the default will be overwritten
			$('form').on('change', '.js-select-phone-default', function()
			{
				$(this).updateHiddenPhoneNumbersInput();
			});

			// Bind add new address
			$('.js-add-address').on('click', function()
			{
				// Find the address template row
				var element = $('.js-tmpl-add-addressrow').removeClass('hide');

				// Insert to target position, one should know that only one '.js-tmpl-add-addressrow' exist since appendTo method
				element.appendTo($(this).closest('.js-nth-schedule-info').find('.js-add-address-position'));
			});

			// Bind save new address
			$('.js-nth-schedule-info').on('click', '.js-save-address', saveAddress);

			// Bind add new telephone
			$('.js-add-tel').on('click', function()
			{
				$(this).closest('.js-tel-wrap').find('.js-tmpl-add-telrow').removeClass('hide');
			});

			// Bind save new telephone
			$('.js-save-tel').on('click', saveTel);

			function saveAddress()
			{
				// The dynamic row wrapper
				var currentWrap = $(this).closest('.js-tmpl-add-addressrow');

				// The hidden input will save the user customized input address, and wait for model to save.
				var targetHiddenInput = $("#" + self.options.createAddressId);

				// Select all the address drop down list, since we have to update all at once
				var targetListToUpdate = $('.js-address-wrap select');

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
					Joomla.renderMessages([
						['欄位輸入不完整']
					]);
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
					targetListToUpdate.each(function()
					{
						$(this).append(html);
						$(this).find('option:last').attr('selected', true);
					});

					// Update to hidden input
					self.updateJsonToInputField(self.options.createAddressId, data);

					// Clear current row
					currentWrap.addClass('hide');

					// Update Schedule date once
					window.DeliverScheduleHandler.updateScheduleDate(
						$('#' + self.options.seeDrDateId).val(),
						$('#' + self.options.periodId).val(),
						self.options.addressesKeys
					);

					Joomla.renderMessages([
						['提醒您，您已新增散客電話或地址，記得按儲存喲。']
					]);
				}
			}

			function saveTel()
			{
				var wrapperElement = $(this).closest('.js-tel-wrap');
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
							var tagId = wrapperElement.find('input[type=hidden]').prop('id');

							data.push({default: 'true', number: phoneToAdd.val()});

							// Perform html update
							self.updatePhoneHtml(tagId, data);

							// Perform hidden input update
							self.updateJsonToInputField(tagId, data);
						}
					}
				}

				// Clear the input value
				phoneToAdd.val("");

				// Hide the input row
				$(this).closest('.js-tmpl-add-telrow').addClass('hide');
			}
		},

		/**
		 * Fire ajax request and get from Customer model and Addresses modl
		 *
		 * @param {int} id
		 */
		fireAjax: function(id)
		{
			var self = this;
			// Fire ajax to Customer
			$.ajax({
				type: "POST",
				url: "index.php?option=com_schedule&task=customer.ajax.json",
				data: {
					id: id
				}
			}).done(function(cdata)
				{
					var cdata = $.parseJSON(cdata);
					var id_number = cdata.id_number;

					try
					{
						// Update phone numbers
						var tel_office = cdata.tel_office;

						console.log(tel_office);

						// Update phone select list
						self.updatePhoneHtml(self.options.telOfficeId, tel_office);
					}
					catch (err)
					{
						self.updatePhoneHtml(self.options.telOfficeId);
					}

					try
					{
						// Update phone numbers
						var tel_home = cdata.tel_home;

						// Update phone select list
						self.updatePhoneHtml(self.options.telHomeId, tel_home);
					}
					catch (err)
					{
						self.updatePhoneHtml(self.options.telHomeId);
					}

					try
					{
						// Update phone numbers
						var mobile = cdata.mobile;

						// Update phone select list
						self.updatePhoneHtml(self.options.mobileId, mobile);
					}
					catch (err)
					{
						self.updatePhoneHtml(self.options.mobileId);
					}

					// Update customer id_number
					self.updateCustomerIdNumber(self.options.customerIdNumber, id_number);

					// Update Birth Date
					self.updateCustomerBirthDate(self.options.birthDateId, cdata.birth_date);

					// Update Hospital
					self.updateCustomerHospital(self.options.hospitalId, cdata.hospital);
				});

			//Fire ajax to Addresses
			$.ajax({
				type: "POST",
				url: "index.php?option=com_schedule&task=addresses.ajax.json",
				data: {
					id: id
				}
			}).done(function(cdata)
				{
					var cdata = $.parseJSON(cdata);

					// Update empty rows of addresses inputs
					for (var i = 0; i < self.options.addressesKeys.length; i++)
					{
						self.updateAddressHtml(self.options.addressesKeys[i], cdata);
					}
				});
		},

		/**
		 * Update customer id_number input value while changing customer_id
		 *
		 * updateCustomerIdNumber
		 *
		 * @param {string} target  Target element id
		 * @param {int}    id      customer_id to update
		 */
		updateCustomerIdNumber: function(target, id)
		{
			id = id || "";

			var targetElement = $('#' + target);

			targetElement.val(id);
		},

		/**
		 * Update customer Birth Date
		 *
		 * @param {string} target  Target element id
		 * @param {string} date    birthday
		 */
		updateCustomerBirthDate: function(target, date)
		{
			date = date || "";

			var targetElement = $('#' + target);

			targetElement.val(date);
		},

		/**
		 * Update customer Hospital
		 *
		 * @param {string} target  Target element id
		 * @param {string} id      Hospital id
		 */
		updateCustomerHospital: function(target, id)
		{
			id = id || "";

			var targetElement = $('#' + target);

			targetElement.val(id);

			targetElement.trigger('liszt:updated');
		},

		/**
		 * Update the hidden input jason file
		 *
		 * @param {string} target    Target element id
		 * @param {json}   dataJson  Data to update
		 */
		updateJsonToInputField: function(target, dataJson)
		{
			dataJson = dataJson || {};

			var targetElement = $('#' + target);

			// Check if selector get null
			if (targetElement.length !== 0)
			{
				targetElement.val(JSON.stringify(dataJson));
			}
		},

		/**
		 * Update address select list row
		 *
		 * @param {string}  key
		 * @param {json}    addressJson
		 */
		updateAddressHtml: function(key, addressJson)
		{
			addressJson = addressJson || {};

			// ex: jform_schedule_1st_address
			var targetId = 'jform_schedules_' + key + '_address_id';

			// ex: jform[schedule_1st][address]
			var targetName = 'jform[' + 'schedules_' + key + '][address_id]';

			// Find its parent, later we will replace it with new select list
			var targetsParent = $('#' + targetId).parent();

			var currentSelected = $('#' + targetId).val();

			var html = '';

			var addressListClass = 'js-address-list';

			// Add select tag
			html += '<select' +
				' name="' + targetName + '"' +
				' id="' + targetId + '"' +
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
		},

		/**
		 * Update phone input list row
		 *
		 * @param {string}  tagId
		 * @param {json}    telJson
		 */
		updatePhoneHtml: function(tagId, telJson)
		{
			telJson = telJson || {};

			var target = $('#' + tagId).parent().find('.controls');
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
					if (telJson[i].number == '')
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
		},

		/**
		 * Every time user select different phone number, the default will be overwritten
		 *
		 * return void
		 */
		updateHiddenPhoneNumbersInput: function()
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
		}
	};
})(jQuery);
