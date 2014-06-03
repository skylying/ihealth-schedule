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
				ERROR_NO_SEE_DR_DATE : 2
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
		},
		/**
		 * registerEvent Bind all relative events, ex: period, times, seeDrDate, weekday and addresses.
		 */
		registerEvent: function()
		{
			var self = this;

			window.MethodFieldHandler.registerEvent();

			window.CustomerFieldHandler.registerEvent();

			// If the new route's weekday is changed, recalculate weekday.
			$('.js-route-weekday select').on('change', function()
			{
				var weekday = $(this).val();
				var nth = $(this).attr('id');

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

			// Trigger once to update show schedule info box
			$('.js-nth-schedule-check input[type=checkbox]').change();

			/**
			 *
			 * Update Schedule's Edit Block
			 *
			 */

			// When 'times' is change show according edit blocks.
			$('#' + self.options.timesId).on('change', function()
			{
				window.DeliverScheduleHandler.showSchedulesEditBlock($(this).val());
			});

			// After 'times' event binding, update edit block once.
			window.DeliverScheduleHandler.showSchedulesEditBlock($('#' + self.options.timesId).val());

			/**
			 *
			 * Update Schedule Date when the following attributes are changed
			 *
			 * 1. seeDrDateId
			 * 2. periodId
			 * 3. js-address-list
			 * 4. Schedule's checkboxes' status
			 *
			 */

			// When see doctor date is changed, update schedule date
			$('#' + self.options.seeDrDateId).on('change', function()
			{
				window.DeliverScheduleHandler.updateScheduleDate();
			});

			// When prescription period is changed, update schedule date
			$('#' + self.options.periodId).on('change', function()
			{
				window.DeliverScheduleHandler.updateScheduleDate();
			});

			// When address list changed, update schedule date
			$('.js-address-wrap').on('change', '.js-address-list', function()
			{
				window.DeliverScheduleHandler.updateScheduleDate();
			});

			// Combine selector, whenever schedule's checkboxes are changed, update 'send date'
			var $scheduleOne = jQuery('#jform_schedules_1st_deliver_nth0');
			var $scheduleTwo = $scheduleOne.add('#jform_schedules_2nd_deliver_nth0');
			var $scheduleAll = $scheduleTwo.add('#jform_schedules_3rd_deliver_nth0');

			$scheduleAll.on('change', function()
			{
				window.DeliverScheduleHandler.updateScheduleDate();
			});
		}
	};

})(jQuery);
