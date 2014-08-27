;
(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.RxIndividualEdit !== undefined)
	{
		return;
	}

	// Exports class RxResidentEditList
	window.RxIndividualEdit = {
		init: function(options)
		{
			this.listeners = {};

			// Overwrite with user's options
			this.options = $.extend({
				customerId           : null,
				customerIdNumber     : null,

				seeDrDateId          : null,
				periodId             : null,
				methodId             : null,

				telOfficeId          : null,
				telHomeId            : null,
				mobileId             : null,

				addressesKeys        : ["1st", "2nd", "3rd"],
				createAddressId      : null,
				timesId              : null,

				drugId               : null,
				deleteDrugId         : null,

				hospitalId           : null,
				birthDateId          : null,

				SUCCESS_ROUTE_EXIST  : 0,
				ERROR_NO_ROUTE       : 1,
				ERROR_NO_SEE_DR_DATE : 2,

				isEdit               : 0
			}, options);

			window.MethodFieldHandler.setOptions({
				methodId             : this.options.methodId,
				drugId               : this.options.drugId,
				deleteDrugId         : this.options.deleteDrugId
			});

			window.DeliverScheduleHandler.setOptions({
				addressesKeys        : this.options.addressesKeys,
				seeDrDateId          : this.options.seeDrDateId,
				periodId             : this.options.periodId,
				SUCCESS_ROUTE_EXIST  : this.options.SUCCESS_ROUTE_EXIST,
				ERROR_NO_ROUTE       : this.options.ERROR_NO_ROUTE,
				ERROR_NO_SEE_DR_DATE : this.options.ERROR_NO_SEE_DR_DATE
			});

			window.CustomerFieldHandler.setOptions({
				customerId           : this.options.customerId,
				customerIdNumber     : this.options.customerIdNumber,
				telOfficeId          : this.options.telOfficeId,
				telHomeId            : this.options.telHomeId,
				mobileId             : this.options.mobileId,
				addressesKeys        : this.options.addressesKeys,
				createAddressId      : this.options.createAddressId,
				seeDrDateId          : this.options.seeDrDateId,
				periodId             : this.options.periodId,
				hospitalId           : this.options.hospitalId,
				birthDateId          : this.options.birthDateId
			});

			this.registerEvent();
			this.triggerEvent();

			// Clear create address data
			$('#' + this.options.createAddressId).val('');
		},

		/**
		 * triggerEvent() will trigger necessary 'change' event after registerEvent()
		 */
		triggerEvent: function()
		{
			// Updated schedules' checkboxes
			$('#' + this.options.timesId).change();

			// Trigger once to toggle the opacity of schedule
			$('.js-nth-schedule-check input[type=checkbox]').change();
		},

		/**
		 * registerEvent() will Bind all relative events, ex: period, times, seeDrDate, weekday and addresses.
		 */
		registerEvent: function()
		{
			var self = this;

			window.MethodFieldHandler.registerEvent();

			window.CustomerFieldHandler.registerEvent();

			// If the new route's weekday is changed, recalculate weekday.
			$('.js-route-weekday select').on('liszt:updated', function()
			{
				var weekday = $(this).val();
				var nth = $(this).prop('id');

				if (nth.indexOf("1st") > -1)
				{
					nth = '1st';
				}
				else if (nth.indexOf("2nd") > -1)
				{
					nth = '2nd';
				}
				else if (nth.indexOf("3rd") > -1)
				{
					nth = '3rd';
				}

				window.DeliverScheduleHandler.updateScheduleDateByWeekday(weekday, nth);
			});

			// This binding will set the edit block to opaque when unchecked
			$('.js-nth-schedule-check input[type=checkbox]').on('change', function()
			{
				window.DeliverScheduleHandler.bindChangeNthScheduleInfo($(this));
			});

			// When 'times' is change show according edit blocks.
			$('#' + self.options.timesId).on('change', function()
			{
				window.DeliverScheduleHandler.showSchedulesEditBlock($(this).val());
			});

			/*
			 * Update Schedule Date when the following attributes are changed
			 *
			 * 1. seeDrDateId
			 * 2. periodId
			 * 3. js-address-list
			 * 4. Schedule's checkboxes' status
			 */
			$([
				'#' + self.options.periodId,
				'#' + self.options.seeDrDateId,
				'#jform_schedules_1st_deliver_nth0',
				'#jform_schedules_2nd_deliver_nth0',
				'#jform_schedules_3rd_deliver_nth0',
				'.js-address-list'
			]).each(
				function(i, selector)
				{
					$(selector).change(function()
					{
						window.DeliverScheduleHandler.updateScheduleDate();
					});
				}
			);
		}
	};

})(jQuery);
