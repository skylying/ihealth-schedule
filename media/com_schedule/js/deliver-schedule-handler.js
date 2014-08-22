;
(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.DeliverScheduleHandler !== undefined)
	{
		return;
	}

	// Exports class DeliverScheduleHandler
	window.DeliverScheduleHandler = {
		setOptions: function(options)
		{
			// Overwrite with user's options
			this.options = $.extend({
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
		 * return void
		 */
		bindChangeNthScheduleInfo: function($checkboxes)
		{
			if ($checkboxes.prop("checked"))
			{
				$checkboxes.closest('.schedules').find('.js-nth-schedule-info').removeClass('opaque');
			}
			else
			{
				$checkboxes.closest('.schedules').find('.js-nth-schedule-info').addClass('opaque');
			}
		},

		/**
		 * Calculate finish drug date, schedule date
		 *
		 * @param {Array}     addressesKeys
		 */
		updateScheduleDate: function(addressesKeys)
		{
			var seeDrDate = $('#' + this.options.seeDrDateId).val();

			// Do nothing when see-dr-date is empty
			if (!seeDrDate)
			{
				return;
			}

			addressesKeys = addressesKeys || this.options.addressesKeys;

			var period = $('#' + this.options.periodId).val();
			var self = this;
			var moment_date = moment(seeDrDate);

			for (var i = 0; i < addressesKeys.length; i++)
			{
				var drugEmptyDateId = '#jform_schedules_' + addressesKeys[i] + '_drug_empty_date';
				var availableReceiveDateId = '#jform_schedules_' + addressesKeys[i] + '_available_receive_date';
				var selectedAddressId = '#jform_schedules_' + addressesKeys[i] + '_address_id';
				var deliveredNth = '#jform_schedules_' + addressesKeys[i] + '_deliver_nth0';

				var address = $(selectedAddressId).val();

				// Set finish drug date
				moment_date.add('days', period);
				$(drugEmptyDateId).val(moment_date.format("YYYY-MM-DD"));

				// 第一次外送的開始領藥日 = 就醫日期
				// 第二次外送的開始領藥日 = 第一次吃完藥日 -10 天
				// 第三次外送的開始領藥日 = 第二次吃完藥日 -10 天
				var moment_receive_date = moment(seeDrDate);

				switch (addressesKeys[i])
				{
					case '2nd':
						moment_receive_date.add('days', parseInt(period));
						moment_receive_date.subtract('days', 10);
						break;
					case '3rd':
						moment_receive_date.add('days', parseInt(period) * 2);
						moment_receive_date.subtract('days', 10);
						break;
				}

				$(availableReceiveDateId).val(moment_receive_date.format("YYYY-MM-DD"));

				if ($(deliveredNth).is(":checked") && address != '')
				{
					var city = $(selectedAddressId).find('option:selected').data('city');
					var area = $(selectedAddressId).find('option:selected').data('area');

					// fire ajax only if address info is sufficient
					if (city && area)
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
					// if Checkbox-1 is unchecked, , check and show it
					if(!checkbox1.prop('checked'))
					{
						checkbox1.prop('checked', true).trigger('change');
					}

					// Unchecked 2, Hide 2
					checkbox2.prop('checked', false).trigger('change');
					schedules2.addClass('hide');

					// Unchecked 3, Hide 3
					checkbox3.prop('checked', false).trigger('change');
					schedules3.addClass('hide');

					break;
				case '2':
					// Checkbox-1 is don't-care.

					// if Checkbox-2 is unchecked, check and show it
					if(!checkbox2.prop('checked'))
					{
						checkbox2.prop('checked', true).trigger('change');
						schedules2.removeClass('hide');
					}

					// Unchecked Checkbox-3, Hide Checkbox-3
					checkbox3.prop('checked', false).trigger('change');
					schedules3.addClass('hide');
					break;
				case '3':
					// Checkbox-1 is don't-care.

					// if Checkbox-2 is unchecked, check and show it
					if(!checkbox2.prop('checked'))
					{
						checkbox2.prop('checked', true).trigger('change');
						schedules2.removeClass('hide');
					}

					// if Checkbox-3 is unchecked, check and show it
					if(!checkbox3.prop('checked'))
					{
						checkbox3.prop('checked', true).trigger('change');
						schedules3.removeClass('hide');
					}
					break;
				default:
					break;
			}
		},

		/**
		 * Calculate finish drug date by specifying weekday
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
