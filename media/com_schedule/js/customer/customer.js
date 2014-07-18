(function ($)
{
	"use strict";

	// Prevent global conflict
	if (typeof window.CustomerJs != 'undefined')
	{
		return;
	}

	window.CustomerJs = {

		// Initialize all element we need
		initialize : function(isNew)
		{
			this.isNew = isNew;

			this.birthday = $('#jform_birth_date');
			this.age      = $('#jform_age');

			// Customer type and their form container <div>
			this.type          = $('#jform_type');
			this.individualDiv = $('#individualdiv');
			this.residentDiv   = $('#residentdiv');

			// Hidden phone input field with json format
			this.officeHiddenInput = $('#jform_tel_office');
			this.homeHiddenInput   = $('#jform_tel_home');
			this.mobileHiddenInput = $('#jform_mobile');
			this.cityHiddenInput   = $('#jform_city');
			this.areaHiddenInput   = $('#jform_area');

			// Visible phone inputs on edit page
			this.officePhones = $('.tel_office_number');
			this.homePhones   = $('.tel_home_number');
			this.mobilePhones = $('.mobile_number');

			// All visible inputs
			this.visibleInputs = $('.visibleinput input');

			// Address handlers
			this.hiddenAddressInput = $('#jform_address');
			this.newAddressBtn      = $('#newaddress');
			this.addressTemplate    = $('#address-template');
			this.addressAppendArea  = $('#address-append-area');

			// Prevent too much keyup event
			this.defaultSwitch = false;

			// Bind all events
			this.bindEvent();
		},

		/**
		 * Bind all events
		 */
		bindEvent : function()
		{
			// Register self as CustomerJs alias
			var self = this;

			// Calculate age on domready & birthday onchange
			$(document).on('ready', this.calculateAge());
			this.birthday.on('change', {self : self}, this.calculateAge);

			// Bind type change event
			this.type.find('input').on('click', function()
			{
				if ($(this).val() == 'individual')
				{
					if (!self.isNew)
					{
						alert('您剛剛編輯的『住民』資料將不會被儲存');
					}

					self.individualDiv.removeClass('hide');
					self.residentDiv.addClass('hide');
				}
				else
				{
					if (!self.isNew)
					{
						alert('您剛剛編輯的『散客』資料將不會被儲存');
					}

					self.individualDiv.addClass('hide');
					self.residentDiv.removeClass('hide');
				}
			});

			/**
			 * On each <span class="glyphicon-ok"> clicked, two things happen :
			 *
			 * 1. turn itself to green background, turn others back to grey
			 * 2. update title to "true" for json save later
			 */
			this.individualDiv.on('click', '.glyphicon-ok', function()
			{
				var spanGroup = $(this).closest('fieldset').find('span');

				$(spanGroup).each(function()
				{
					$(this).attr('title', 'false');
					$(this).removeClass('default');
				});

				$(this).attr('title', 'true');
				$(this).addClass('default');
			});

			// When user type new phone number, auto default this one
			this.visibleInputs.on('keyup', function()
			{
				if (self.defaultSwitch == false)
				{
					$(this).siblings().trigger('click');

					// Stop triggering keyup event
					self.defaultSwitch = true;
				}
			});

			// Reset defaultSwitch
			this.visibleInputs.on('focusout', function()
			{
				self.defaultSwitch = false;
			});

			// Add new address button
			this.newAddressBtn.on('click', function()
			{
				var template = self.addressTemplate.html();

				self.addressAppendArea.append(template);

				// Trigger afterInsert event, later .address-row will catch it
				$('.address-row').trigger('afterInsert');
			});

			/**
			 * update select style using chosen() after insert a new row
			 */
			this.addressAppendArea.on('afterInsert', '.address-row', function()
			{
				$(this).find('select').chosen();
			});

			// Delete address button
			this.addressAppendArea.on('click', '.deleteaddress', function()
			{
				if (confirm('確定要刪除此筆地址嗎?'))
				{
					$(this).closest('.address-row').remove();

					var isDefault = $(this).closest('.address-row').find('.glyphicon-ok').attr('title');

					// If user delete the default address, we automatically set first existed address as default
					if (isDefault == 'true')
					{
						self.setDefaultAddress();
					}
				}
			});

			/**
			 * 用 $.get ajax 實做地址區域連動功能
			 */
			this.addressAppendArea.on('change', '.citylist', function()
			{
				var cityId = $(this).val(),
					areaDiv = $(this).parent().siblings(),
					url = window.location.origin + window.location.pathname + '?option=com_schedule';

				// Send ajax
				$.get(url, { task: "customer.ajax.address", city : cityId })
					.done(function(data){

						// Replace area list
						$(areaDiv).html(data);

						// Trigger afterAjax event, later .areadiv will catch it
						$(areaDiv).trigger('afterAjax');
					});
			});

			/**
			 * update select style using chosen() after ajax fired
			 */
			this.addressAppendArea.on('afterAjax', '.areadiv', function()
			{
				$(this).find('select').chosen();
			});

			// Update jform[city]
			this.addressAppendArea.on('change', '.citylist', function()
			{
				self.cityHiddenInput.val($(this).val());
			});

			// Update jform[area]
			this.addressAppendArea.on('change', '.arealist', function()
			{
				self.areaHiddenInput.val($(this).val());
			});

			// When user type new address, auto default this one
			this.addressAppendArea.on('keyup', '.roadname', function()
			{
				if (self.defaultSwitch == false)
				{
					var addressRow = $(this).closest('.address-row');

					addressRow.find('.glyphicon-ok').trigger('click');

					// Stop triggering keyup event
					self.defaultSwitch = true;
				}
			});

			// Reset defalutSwitch
			this.addressAppendArea.on('focusout', '.roadname', function()
			{
				self.defaultSwitch = false;
			});
		},

		/**
		 * Calculate age on document ready and birthday onchange
		 *
		 * @event {object}
		 */
		calculateAge : function(event)
		{
			var self = this;

			// If calculateAge() was called by onchange event, need to define self as CustomerJS object
			if (typeof event !== 'undefined')
			{
				self = event.data.self;
			}

			var today    = new Date,
				birthday = new Date(self.birthday.val()),
				age      = today.getFullYear() - birthday.getFullYear();

			// Set birth year to this year
			birthday.setFullYear(today.getFullYear());

			// 還沒過生日就扣掉一歲
			if (today > birthday)
			{
				age--;
			}

			// BJ4
			if (today.getDate() == birthday.getDate())
			{
				var happyBirthday = ' ' +
					'<button type="button" class="btn btn-default" style="background: #ff78bb; color: white">' +
					'<span class="glyphicon glyphicon-music"></span> 生日快樂</button>';

				self.birthday.after(happyBirthday);
			}

			// Update age field
			self.age.val(age);
		},

		/**
		 * Remove unnecessary form, will be called on form submit
		 */
		removeForm : function()
		{
			var formType = this.type.find('input:checked').val();

			(formType == 'individual') ? this.residentDiv.remove() : this.individualDiv.remove();
		},

		/**
		 * Build and put back real telephone json string, will be called on form submit
		 */
		updatePhoneJson : function()
		{
			// Get hidden json input value
			var officeResult = this.buildHiddenJson(this.officePhones);
			var homeResult   = this.buildHiddenJson(this.homePhones);
			var mobileResult = this.buildHiddenJson(this.mobilePhones);

			// Update json
			this.officeHiddenInput.val(officeResult);
			this.homeHiddenInput.val(homeResult);
			this.mobileHiddenInput.val(mobileResult);
		},

		/**
		 * Build each phone json strings
		 *
		 * @data {array}
		 *
		 * @returns {string}
		 */
		buildHiddenJson : function(data)
		{
			var result = [];

			data.each(function()
			{
				var tmp      = {},
					isDefult = ($(this).siblings().attr('title') == 'true'),
					phone    = $(this).val();

				tmp["default"] = isDefult;
				tmp["number"]  = phone;

				result.push(tmp);
			});

			return JSON.stringify(result);
		},

		/**
		 * Update hidden address input, will be called on form submit
		 */
		updateAddressJson : function()
		{
			var result = [];

			this.addressAppendArea.find('.address-row').each(function()
			{
				var tmp = {};

				tmp["city"] = $(this).find('select[name="city"]').val();
				tmp["area"] = $(this).find('select[name="area"]').val();
				tmp["address"] = $(this).find('.roadname').val();
				tmp["previous"] = ($(this).find('span').attr('title') == 'true') ? 1 : 0;

				result.push(tmp);
			});

			this.hiddenAddressInput.val(JSON.stringify(result));
		},

		/*
		 * Check and set default address after original default address was deleted
		 */
		setDefaultAddress : function()
		{
			var defaultMarkers = $('.address-row').find('.glyphicon-ok');

			// If there is no address, nothing happen
			if (defaultMarkers.length == 0)
			{
				return;
			}
			else
			{
				// Has address, then we set first one as default
				$(defaultMarkers[0]).addClass('default').attr('title', 'true');
			}
		}
	};
})(jQuery);
