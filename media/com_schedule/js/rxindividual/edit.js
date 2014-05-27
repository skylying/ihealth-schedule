;
(function ($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.RxIndividualEdit !== undefined)
	{
		return;
	}

	// Exports class RxResidentEditList
	window.RxIndividualEdit = {
		init: function (options)
		{
			this.listeners = {};

			// Overwrite with user's options
			this.options = $.extend(true, {
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

				SUCCESS_ROUTE_EXIST  : 0,
				ERROR_NO_ROUTE       : 1,
				ERROR_NO_SEE_DR_DATE : 2
			}, options);

			// init method class
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
				periodId             : this.options.periodId
			});
		},
		/**
		 * Run
		 */
		run: function ()
		{
			var self = this;

			window.MethodFieldHandler.run();

			window.CustomerFieldHandler.run();

			// Bind 'change' event to 'weekday of new route data'
			$('.js-route-weekday select').on('change', function ()
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

			// Toggle nth schedules
			$('.js-nth-schedule-check input[type=checkbox]').on('change', function ()
			{
				window.DeliverScheduleHandler.bindChangeNthScheduleInfo($(this));
			});

			// Bind 'times' event to 'show nth prescriptions'
			$('#' + self.options.timesId).on('change', function ()
			{
				window.DeliverScheduleHandler.showSchedulesEditBlock($(this).val());
			});

			// After 'times' event binding, update edit block once.
			window.DeliverScheduleHandler.showSchedulesEditBlock($('#' + self.options.timesId).val());

			// Bind See Doctor Date on change, update schedule date
			$('#' + self.options.seeDrDateId).on('change', function ()
			{
				window.DeliverScheduleHandler.updateScheduleDate(
					$('#' + self.options.seeDrDateId).val(),
					$('#' + self.options.periodId).val(),
					self.options.addressesKeys
				);
			});

			// Bind Drug period on change, update schedule date
			$('#' + self.options.periodId).on('change', function ()
			{
				window.DeliverScheduleHandler.updateScheduleDate(
					$('#' + self.options.seeDrDateId).val(),
					$('#' + self.options.periodId).val(),
					self.options.addressesKeys
				);
			});

			// Bind address list on change, update schedule date
			$('.js-address-wrap').on('change', '.js-address-list', function ()
			{
				window.DeliverScheduleHandler.updateScheduleDate(
					$('#' + self.options.seeDrDateId).val(),
					$('#' + self.options.periodId).val(),
					self.options.addressesKeys
				);
			});

			// Combine selector, whenever schedule's checkboxes are changed, update 'send date'
			var $scheduleOne = jQuery('#jform_schedules_1st_deliver_nth0');
			var $scheduleTwo = $scheduleOne.add('#jform_schedules_2nd_deliver_nth0');
			var $scheduleAll = $scheduleTwo.add('#jform_schedules_3rd_deliver_nth0');

			$scheduleAll.on('change', function ()
			{
				window.DeliverScheduleHandler.updateScheduleDate(
					$('#' + self.options.seeDrDateId).val(),
					$('#' + self.options.periodId).val(),
					self.options.addressesKeys
				);
			});
		}
	};

})(jQuery);
