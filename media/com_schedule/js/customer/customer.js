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
		initialize : function(option)
		{
			this.isNew = option.isNew;

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

			// Institute Id Element
			this.$instituteId = $('#jform_institute_id');

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
			 * 3. Check empty phone number, if it's empty, return and alert user
			 */
			this.individualDiv.on('click', '.glyphicon-ok', function()
			{
				var spanGroup = $(this).closest('fieldset').find('span'),
					input = $(this).parent().find('input');

				// Check empty phone number
				if (input.length > 0 && !input.val())
				{
					alert('不可將空白電話存成預設');

					return;
				}

				$(spanGroup).each(function()
				{
					$(this).attr('title', 'false');
					$(this).removeClass('default');
				});

				$(this).attr('title', 'true');
				$(this).addClass('default');
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

			var date = self.birthday.val();

			if (!date)
			{
				return;
			}

			var today    = new Date,
				birthday = new Date(date),
				age      = today.getFullYear() - birthday.getFullYear();

			// Set birth year to this year
			birthday.setFullYear(today.getFullYear());

			// 還沒過生日就扣掉一歲
			if (age > 0 && today > birthday)
			{
				age--;
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
				var phone = $.trim($(this).val());

				if (phone)
				{
					var tmp      = {},
						isDefult = ($(this).siblings().attr('title') == 'true');

					tmp["default"] = isDefult;
					tmp["number"]  = phone;

					result.push(tmp);
				}
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
			var defaultMarker = $('.address-row .glyphicon-ok:first');

			// Has address, then we set first one as default
			if (defaultMarker.length > 0)
			{
				$(defaultMarker).addClass('default').attr('title', 'true');
			}
		},

		/**
		 * Validate birthday
		 */
		validateBirthday: function()
		{
			var birthday = this.birthday.val();

			if ('' === birthday)
			{
				return true;
			}

			var pattern = /^[12][0-9]{3}-[01][0-9]-[0-3][0-9]/;

			return birthday.match(pattern) ? true : false;
		},

		/**
		 * Triggered by institute_id onchange event
		 *
		 * It will return a callback function, which contains params:
		 * - {object}  e     Event object, Contains the following custom properties:
		 *                        - val:     The current selection (taking into account the result of the change) - id or array of ids.
		 *                        - added:   The added element, if any - the full element object, not just the id.
		 *                        - removed: The removed element, if any - the full element object, not just the id.
		 * - {element} $node Current Element node
		 */
		instituteIdChange: function()
		{
			var self = this;

			return function(e, $node)
			{
				var data = $node.select2('data');

				self.$instituteId.val(data.id);
			};
		}
	};
})(jQuery);
