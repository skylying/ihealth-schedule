;
(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.DeliverScheduleHandler !== undefined)
	{
		return;
	}

	// Exports class RxResidentEditList
	window.DeliverScheduleHandler = {
		setOptions: function(options)
		{
			// Overwrite with user's options
			this.options = $.extend(true, {
				addressesKeys        : ["1st", "2nd", "3rd"],
				seeDrDateId          : null,
				periodId             : null,
				SUCCESS_ROUTE_EXIST  : 0,
				ERROR_NO_ROUTE       : 1,
				ERROR_NO_SEE_DR_DATE : 2
			}, options);
		},

		/**
		 * Bind change schedules' cheboxes
		 *
		 * bindChangeNthScheduleInfo
		 *
		 * return void
		 */
		bindChangeNthScheduleInfo: function($cheboxes)
		{
			$cheboxes.on('change', toggleNthScheduleInfo($(this)));

			toggleNthScheduleInfo($cheboxes);

			/**
			 * Bind schedules' checkboxes on 'toggle' opaque effect and 'update sender date' method
			 *
			 * toggleNthScheduleInfo
			 *
			 * return void
			 */
			function toggleNthScheduleInfo($cheboxes)
			{
				if ($cheboxes.prop("checked"))
				{
					$cheboxes.closest('.schedules').find('.js-nth-schedule-info').removeClass('opaque');
				}
				else
				{
					$cheboxes.closest('.schedules').find('.js-nth-schedule-info').addClass('opaque');
				}
			}
		},

		/**
		 * Calculate finish drug date, schedule date
		 *
		 * updateScheduleDate
		 *
		 * @param {string}    seeDrDate
		 * @param {json}      period
		 */
		updateScheduleDate: function(seeDrDate, period, addressesKeys)
		{
			var self = this;
			var moment_date = moment(seeDrDate);

			for (var i = 0; i < addressesKeys.length; i++)
			{
				var drugEmptyDateId = '#jform_schedules_' + addressesKeys[i] + '_drug_empty_date';
				var selectedAddressId = '#jform_schedules_' + addressesKeys[i] + '_address_id';
				var deliveredNth = '#jform_schedules_' + addressesKeys[i] + '_deliver_nth0';

				var address = $(selectedAddressId).val();

				// Set finish drug date
				moment_date.add('days', period);
				$(drugEmptyDateId).val(moment_date.format("YYYY-MM-DD"));

				if ($(deliveredNth).is(":checked") && address != '')
				{
					var city = $(selectedAddressId).find('option:selected').attr('city');
					var area = $(selectedAddressId).find('option:selected').attr('area');

					// fire ajax only if address info is sufficient
					if(city && area)
					{
						$.ajax({
							type: "POST",
							url: "index.php?option=com_schedule&task=rxindividual.ajax.senddate",
							data: {
								nth: addressesKeys[i],
								city_id: city,
								area_id: area,
								see_dr_date: seeDrDate,
								period: period
							}
						}).done(function(cdata)
							{
								var data = JSON.parse(cdata);

								var sendDateId = '#jform_schedules_' + data['nth'] + '_date';

								if (data['type'] == self.options.SUCCESS_ROUTE_EXIST)
								{
									$(sendDateId).closest('.js-nth-schedule-info').find('.js-route-wrap').addClass('hide');

									$(sendDateId).val(data['date']);
								}
								else
								{
									if (data['type'] == self.options.ERROR_NO_ROUTE)
									{
										$(sendDateId).closest('.js-nth-schedule-info').find('.js-route-wrap').removeClass('hide');
									}
								}
							});
					}
				}
			}
		},

		/**
		 * Show and hide schedules edit box by changing deliver_nth
		 *
		 * showSchedulesEditBlock
		 *
		 * return void
		 *
		 */
		showSchedulesEditBlock: function(times)
		{
			var schedules1 = $('.schedules').eq(0);
			var schedules2 = $('.schedules').eq(1);
			var schedules3 = $('.schedules').eq(2);

			var checkbox1 = schedules1.find('.js-nth-schedule-check input[type=checkbox]');
			var checkbox2 = schedules2.find('.js-nth-schedule-check input[type=checkbox]');
			var checkbox3 = schedules3.find('.js-nth-schedule-check input[type=checkbox]');

			switch (times)
			{
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
		},

		/**
		 * Calculate finish drug date by specifying weekday
		 *
		 * updateScheduleDateByWeekday
		 *
		 * @param {string}    weekday
		 * @param {string}    nth
		 */
		updateScheduleDateByWeekday: function(weekday, nth)
		{
			var self = this;
			var seeDrDate = $('#' + this.options.seeDrDateId).val();
			var period = $('#' + this.options.periodId).val();

			$.ajax({
				type: "POST",
				url: "index.php?option=com_schedule&task=rxindividual.ajax.senddate",
				data: {
					nth: nth,
					see_dr_date: seeDrDate,
					period: period,
					weekday: weekday
				}
			}).done(function(cdata)
				{
					var data = JSON.parse(cdata);

					var sendDateId = '#jform_schedules_' + data['nth'] + '_date';

					if (data['type'] == self.options.SUCCESS_ROUTE_EXIST)
					{
						$(sendDateId).val(data['date']);
					}
					else
					{
						if (data['type'] == self.options.ERROR_NO_ROUTE)
						{
							$(sendDateId).closest('.js-nth-schedule-info').find('.js-route-wrap').removeClass('hide');

							$(sendDateId).val('');
						}
					}
				});
		}
	};
})(jQuery);
