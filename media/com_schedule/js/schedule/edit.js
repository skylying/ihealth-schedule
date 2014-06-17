;(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.ScheduleEdit !== undefined)
	{
		return;
	}

	// Private selectors
	var $instituteCityTitle,
		$instituteAreaTitle,
		$instituteAddress,
		$memberSelection,
		$addressSelection;

	/**
	 * Class ScheduleEdit
	 */
	window.ScheduleEdit = {
		/**
		 * @type {string}
		 */
		instituteApi: '',
		/**
		 * @type {string}
		 */
		membersApi: '',
		/**
		 * @type {string}
		 */
		addressesApi: '',
		/**
		 * Options
		 *
		 * @param {Object} options
		 * @param {string} options.membersApi   (required) Members Api url
		 * @param {string} options.addressesApi (required) Addresses Api url
		 */
		run: function(options)
		{
			this.instituteApi = options.instituteApi;
			this.membersApi = options.membersApi;
			this.addressesApi = options.addressesApi;

			// Initialize selectors
			$instituteCityTitle = $('#jform_city_title');
			$instituteAreaTitle = $('#jform_area_title');
			$instituteAddress = $('#jform_address');
			$memberSelection = $('#jform_member_id');
			$addressSelection = $('#jform_address_id');
		},

		/**
		 * updateInstituteRelatedInfo
		 *
		 * @param {Object} elem The institute selection element
		 */
		updateInstituteRelatedInfo: function(elem)
		{
			var instituteId = parseInt($(elem).val());

			$.getJSON(this.instituteApi + instituteId, function(institute)
			{
				$instituteCityTitle.val(institute.city_title);
				$instituteAreaTitle.val(institute.area_title);
				$instituteAddress.val(institute.address);
			});
		},

		/**
		 * updateCustomerRelatedInfo
		 *
		 * @param {Object} elem The customer selection element
		 */
		updateCustomerRelatedInfo: function(elem)
		{
			var self = this,
				customerId = parseInt($(elem).val());

			if (isNaN(customerId))
			{
				$memberSelection.html('');
				$memberSelection.trigger('liszt:updated');

				$addressSelection.html('');
				$addressSelection.trigger('liszt:updated');

				return;
			}

			$memberSelection.attr('disabled', true);
			$memberSelection.trigger('liszt:updated');

			$addressSelection.attr('disabled', true);
			$addressSelection.trigger('liszt:updated');

			// Update member selection
			$.getJSON(self.membersApi + customerId, function(members)
			{
				var html = '';

				$.each(members, function(i, member)
				{
					html += '<option value="' + member.id + '">' + member.name + '</option>';
				});

				$memberSelection.html(html);
				$memberSelection.attr('disabled', false);
				$memberSelection.trigger('liszt:updated');
			});

			// Update address selection
			$.getJSON(self.addressesApi + customerId, function(addresses)
			{
				var html = '';

				$.each(addresses, function(i, address)
				{
					var fullAddress = address.city_title + address.area_title + address.address;

					html += '<option value="' + address.id + '">' + fullAddress + '</option>';
				});

				$addressSelection.html(html);
				$addressSelection.attr('disabled', false);
				$addressSelection.trigger('liszt:updated');
			});
		}
	};
})(jQuery);
