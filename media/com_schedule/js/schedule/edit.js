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
		schedulesUrl: '',

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
			this.schedulesUrl = options.schedulesUrl;
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
		 * updateScheduleLink
		 *
		 * @param {Object} elem The institute selection element
		 * @param {string} type Type of "individual" or "institute"
		 */
		updateScheduleLink: function(elem, type)
		{
			var date = $(elem).val(),
				start, end, url;

			if (!date.match(/\d{4}-\d{1,2}-\d{1,2}/))
			{
				return;
			}

			switch (type)
			{
				case 'individual':
				case 'institute':
					start = new Date(date);
					end = new Date(date);

					// Shift 7 days before
					start.setTime(start.getTime() - (7 * 86400000));

					// Shift 7 days after
					end.setTime(end.getTime() + (7 * 86400000));

					url = this.schedulesUrl +
						'&filter[schedule.date_start]=' + start.toISOString().substr(0, 10) +
						'&filter[schedule.date_end]=' + end.toISOString().substr(0, 10);

					if ('institute' === type)
					{
						url += '&filter[schedule.institute_id]=' + $('#jform_institute_id').val();
					}

					$('#' + type + '-schedules-with-range').attr('href', url);

					break;
			}
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
