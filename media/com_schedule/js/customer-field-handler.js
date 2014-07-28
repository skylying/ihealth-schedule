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

		registerEvent: function()
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
			$('.js-nth-schedule-info').on('click', '.js-save-address', function()
			{
				self.saveAddress(this);
			});

			$('.js-nth-schedule-info').on('click', '.js-cancel-address', function()
			{
				$(this).closest('.js-tmpl-add-addressrow').addClass('hide');
			});

			// Bind address list being changed
			$('.js-nth-schedule-info').on('change', '.js-address-list', function()
			{
				var currentSchedule = [];

				currentSchedule.push(
					$(this).closest('.schedules').find('input[id$="_deliver_nth0"]').val()
				);

				self.getSenderWeekdayDataFromAddress(this);

				window.DeliverScheduleHandler.updateScheduleDate(currentSchedule);
			});

			// Bind add new telephone
			$('.js-add-tel').on('click', function()
			{
				$(this).closest('.js-tel-wrap').find('.js-tmpl-add-telrow').removeClass('hide');
			});

			// Bind save new telephone
			$('.js-save-tel').on('click', function()
			{
				self.saveTel(this);
			});

			// While no route, we have to set sender_id and bind this info to address
			$('select[id$="_sender_id"]').on('change', function()
			{
				self.updateSenderIdForAddress(this);
			});

			// While no route, we have to set weekday and bind this info to address
			$('select[id$="_weekday"]').on('change', function()
			{
				self.updateWeekdayForAddress(this);
			});
		},

		/**
		 * While address is being changed, if data-sender and data-weekday exist, set the sender and weekday
		 * dropdown-list these value
		 *
		 * @param self
		 */
		getSenderWeekdayDataFromAddress: function(self)
		{
			var routeWrap = $(self).closest('.js-nth-schedule-info').find('.js-route-wrap');
			var sender_id = $(self).find('option:selected').data('sender');
			var weekday = $(self).find('option:selected').data('weekday');

			if (sender_id)
			{
				routeWrap.find('select[id$="_sender_id"]')
					.find('option[value="' + sender_id + '"]')
					.attr("selected", "selected");

				routeWrap.find('select[id$="_sender_id"]').trigger('liszt:updated');
			}

			if (weekday)
			{
				routeWrap.find('select[id$="_weekday"]')
					.find('option[value="' + weekday + '"]')
					.attr("selected", "selected");

				routeWrap.find('select[id$="_weekday"]').trigger('liszt:updated');
			}
		},

		/**
		 * If the route does not exist, we have to create one and assign sender_id
		 *
		 * @param self
		 */
		updateSenderIdForAddress: function(self)
		{
			var currentAddress = $(self).closest('.js-nth-schedule-info').find('.js-address-list option:selected');

			if (currentAddress.data('sender'))
			{
				currentAddress.data("sender", $(self).val());
			}
			else
			{
				currentAddress.attr("data-sender", $(self).val());
			}

			$('.js-address-list option').each(function()
			{
				if ($(this).val() == currentAddress.val())
				{
					if ($(this).data('sender'))
					{
						$(this).data("sender", $(self).val());
					}
					else
					{
						$(this).attr("data-sender", $(self).val());
					}

					// The sender_id will be change only if the same address is selected.
					if ($(this).is(':selected'))
					{
						$(this).closest('.js-nth-schedule-info')
							.find('select[id$="_sender_id"]')
							.val($(self).val())
							.trigger('liszt:updated');
					}
				}
			});
		},

		/**
		 * If the route does not exist, we have to create one and assign weekday
		 *
		 * @param self
		 */
		updateWeekdayForAddress: function(self)
		{
			var currentAddress = $(self).closest('.js-nth-schedule-info').find('.js-address-list option:selected');

			if (currentAddress.data('weekday'))
			{
				currentAddress.data("weekday", $(self).val());
			}
			else
			{
				currentAddress.attr("data-weekday", $(self).val());
			}

			$('.js-address-list option').each(function()
			{
				if ($(this).val() == currentAddress.val())
				{
					if ($(this).data('weekday'))
					{
						$(this).data("weekday", $(self).val());
					}
					else
					{
						$(this).attr("data-weekday", $(self).val());
					}

					// The weekday will be change only if the same address is selected.
					if ($(this).is(':selected'))
					{
						$(this).closest('.js-nth-schedule-info')
							.find('select[id$="_weekday"]')
							.val($(self).val())
							.trigger('liszt:updated');
					}
				}
			});
		},

		saveTel : function(self)
		{
			var wrapperElement = $(self).closest('.js-tel-wrap');
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

							Joomla.renderMessages([
								['提醒您，您已新增散客電話或地址，記得按儲存喲。']
							]);

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
						this.updatePhoneHtml(tagId, data);

						// Perform hidden input update
						this.updateJsonToInputField(tagId, data);

						// Update Telephones for each schedule

						// Slice "jform_"
						var tag = tagId.slice(6, tagId.length);

						for (var i = 0; i < this.options.addressesKeys.length; i++)
						{
							this.updateTelephoneHtmlForSchedule(this.options.addressesKeys[i], data, tag);
						}

						Joomla.renderMessages([
							['提醒您，您已新增散客電話或地址，記得按儲存喲。']
						]);
					}
				}
				else
				{
					Joomla.renderMessages([
						['提醒您，電話目前上限為最多三筆。']
					]);
				}
			}

			// Clear the input value
			phoneToAdd.val("");

			// Hide the input row
			$(self).closest('.js-tmpl-add-telrow').addClass('hide');
		},

		saveAddress : function(self)
		{
			// The dynamic row wrapper
			var currentWrap = $(self).closest('.js-tmpl-add-addressrow');

			var currentAddressList = $(self).closest('.js-nth-schedule-info').find('.js-address-wrap select');

			// The hidden input will save the user customized input address, and wait for model to save.
			var targetHiddenInput = $("#" + this.options.createAddressId);

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

			// Validate necessary fields
			if ((objectToAdd.city == '')
				|| (objectToAdd.area == '')
				|| (objectToAdd.address == ''))
			{
				// Notify user to make sure they input correctly
				Joomla.renderMessages([
					['欄位輸入不完整']
				]);

				return;
			}
			data.push(objectToAdd);

			// Concatenate string.
			resultString = currentWrap.find('#jform_city option:selected').text() +
				currentWrap.find('#jform_area option:selected').text() +
				objectToAdd.address;

			// Form up html <option>
			html = '<option' +
				' data-city="' + objectToAdd.city + '"' +
				' data-area="' + objectToAdd.area + '"' +
				' value="' + objectToAdd.id + '">' +
				resultString +
				'</option>';

			// Update drop down list at once
			targetListToUpdate.each(function()
			{
				$(this).append(html);

				if($(this).is(currentAddressList))
				{
					$(this).find('option:last').attr('selected', true);
				}
			});

			// Update to hidden input
			this.updateJsonToInputField(this.options.createAddressId, data);

			// Clear current row
			currentWrap.addClass('hide');

			// Update Schedule date once
			window.DeliverScheduleHandler.updateScheduleDate();

			Joomla.renderMessages([
				['提醒您，您已新增散客電話或地址，記得按儲存喲。']
			]);
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

						// Update phone select list
						self.updatePhoneHtml(self.options.telOfficeId, tel_office);
						self.updateJsonToInputField(self.options.telOfficeId, tel_office);

						// Update Telephones for each schedule
						for (var i = 0; i < self.options.addressesKeys.length; i++)
						{
							self.updateTelephoneHtmlForSchedule(self.options.addressesKeys[i], tel_office, 'tel_office');
						}
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
						self.updateJsonToInputField(self.options.telHomeId, tel_home);

						// Update Telephones for each schedule
						for (var i = 0; i < self.options.addressesKeys.length; i++)
						{
							self.updateTelephoneHtmlForSchedule(self.options.addressesKeys[i], tel_home, 'tel_home');
						}
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
						self.updateJsonToInputField(self.options.mobileId, mobile);

						// Update Telephones for each schedule
						for (var i = 0; i < self.options.addressesKeys.length; i++)
						{
							self.updateTelephoneHtmlForSchedule(self.options.addressesKeys[i], mobile, 'mobile');
						}
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
		 * After telephone date retrived from ajax, update html
		 *
		 * @param {strinf} key
		 * @param {json}   telJson
		 * @param {string} fieldId
		 */
		updateTelephoneHtmlForSchedule : function(key, telJson, fieldId)
		{
			telJson = telJson || {};

			// ex: jform_schedule_1st_tel_office
			var targetId = 'jform_schedules_' + key + '_' + fieldId;

			// ex: jform[schedule_1st][address]
			var targetName = 'jform[' + 'schedules_' + key + '][' + fieldId + ']';

			// Find its parent, later we will replace it with new select list
			var targetsParent = $('#' + targetId).parent();

			var targetValue = $('#' + targetId).val();

			var html = '';

			// Add select tag
			html += '<select' +
				' name="' + targetName + '"' +
				' id="' + targetId + '"' +
				'>';

			for (var i = 0; i < telJson.length; i++)
			{
				// Add option tag
				html += '<option' +
					' value="' + telJson[i].number + '"' +
					'>' +
					telJson[i].number +
					'</option>';
			}

			html += '</select>';

			//Clear target hook's html first.
			targetsParent.html("");

			targetsParent.html(html);

			targetsParent.find('option[value="' + targetValue + '"]').attr("selected", "selected");
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
					' data-city="' + addressJson[i].city + '"' +
					' data-area="' + addressJson[i].area + '"' +
					' data-value="' + addressJson[i].id + '"' +
					' value="' + addressJson[i].id + '"' +
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

			targetsParent.find('option[value="' + currentSelected + '"]').attr("selected", "selected");
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
