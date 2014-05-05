;(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.RxResidentEditList !== undefined)
	{
		return;
	}

	// Exports class RxResidentEditList
	window.RxResidentEditList = {
		/**
		 * Run
		 */
		run: function()
		{
			var $panel = $('#rx-list').find('tbody'),
				handler = new MultiRowHandler({$panel:$panel});

			// Bind initialize event
			handler.on('initializeRow', function ($row)
			{
				$row.find('.datetimepicker').datetimepicker({
					pickTime: false
				});

				// 可調劑次數與處方箋外送次數連動
				$row.find('.times').on('change', function()
				{
					var timesValue = $(this).find('option:selected').val(),
						cb1 = $(this).closest('tr').find('input[value="1st"]'),
						cb2 = $(this).closest('tr').find('input[value="2nd"]'),
						cb3 = $(this).closest('tr').find('input[value="3rd"]');

					switch (timesValue)
					{
						case '1':
							cb1.attr('checked', true);

							cb2.attr('checked', false);
							cb2.attr('disabled', true);

							cb3.attr('checked', false);
							cb3.attr('disabled', true);

							break;

						case '2':
							cb1.attr('checked', false);

							cb2.attr('checked', true);
							cb2.attr('disabled', false);

							cb3.attr('checked', false);
							cb3.attr('disabled', true);

							break;

						case '3':
							cb1.attr('checked', false);

							cb2.attr('checked', true);
							cb2.attr('disabled', false);

							cb3.attr('checked', true);
							cb3.attr('disabled', false);

							break;
					}
				})
			});

			// Add row
			$('.button-add-row').click(function ()
			{
				var i,
					amount = $('#new-row-number').val();

				amount = isNaN(amount) ? 0 : amount;

				for (i = 0; i < amount; ++i)
				{
					handler.insert($('#row-template').html());
				}
			});

			// Delete row
			$panel.on('click', '.button-delete-row', function ()
			{
				handler.remove($(this).closest('tr'));
			});

			// Copy row
			$panel.on('click', '.button-copy-row', function ()
			{
				handler.duplicate($(this).closest('tr'));
			});
		}
	};

	// Institute_id select2 onchange handler
	window.updateDeliveryDay = function(e, $node)
	{
		var translateWeek  = {
			MON: '週一',
			TUE: '週二',
			WED: '週三',
			THU: '週四',
			FRI: '週五',
			SAT: '週六',
			SUN: '週日'
		};

		var weekday = e.added.delivery_day,
			color   = e.added.color;

		var weekdaySpan = $('#weekday-from-js'),
			colorBlock  = $('.deliverycolor');

		// update delivery_day
		weekdaySpan.text(translateWeek[weekday]);

		// update delivery_day color
		colorBlock.css('background', color);
	};

})(jQuery);
