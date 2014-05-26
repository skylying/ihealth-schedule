;(function($, undefined)
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
			this.options = $.extend(true, {
				customerID 	         : null,
				customerIDNumber     : null,

				seeDrDateID          : null,
				periodID             : null,
				methodID             : null,

				telOfficeID          : null,
				telHomeID            : null,
				mobileID             : null,

				addressesKeys        : ["1st", "2nd", "3rd"],
				createAddressID      : null,
				timesID              : null,

				drugID               : null,
				deleteDrugID         : null,

				SUCCESS_ROUTE_EXIST  : 0,
				ERROR_NO_ROUTE       : 1,
				ERROR_NO_SEE_DR_DATE : 2
			}, options);
		},
		/**
		 * Run
		 */
		run: function()
		{
			var self = this;

			var method = new MethodFieldHandler({
				methodID     : self.options.methodID,
				drugID       : self.options.drugID,
				deleteDrugID : self.options.deleteDrugID
			});
			method.run();

			// Bind 'change' event to 'weekday of new route data'
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

				self.updateScheduleDateByWeekday(weekday, nth);
			});

			// Bind 'times' event to 'show nth prescriptions'
			$('#' + self.options.timesID).on('change', function ()
			{
				self.showSchedulesEditBlock($(this).val());
			});
		},

		/**
		 * Show and hide schedules edit box by changing send drug times
		 *
		 * showSchedulesEditBlock
		 *
		 * return void
		 *
		 */
		showSchedulesEditBlock : function (times)
		{
			var schedules1 = $('.schedules').eq(0);
			var schedules2 = $('.schedules').eq(1);
			var schedules3 = $('.schedules').eq(2);

			var checkbox1 = schedules1.find('.js-nth-schedule-check input');
			var checkbox2 = schedules2.find('.js-nth-schedule-check input');
			var checkbox3 = schedules3.find('.js-nth-schedule-check input');

			switch(times){
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
		updateScheduleDateByWeekday : function (weekday, nth)
		{
			var self = this;
			var seeDrDate = $('#' + this.options.seeDrDateID).val();
			var period = $('#' + this.options.period).val();

			$.ajax({
				type: "POST",
				url : "index.php?option=com_schedule&task=rxindividual.ajax.senddate",
				data: {
					nth : nth,
					see_dr_date : seeDrDate ,
					period : period ,
					weekday : weekday
				}
			}).done(function (cdata)
				{
					var data = JSON.parse(cdata);

					var sendDateId = '#jform_schedules_' + data['nth'] + '_date';

					if (data['type'] == self.options.SUCCESS_ROUTE_EXIST )
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
