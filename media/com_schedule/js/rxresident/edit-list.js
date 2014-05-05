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
})(jQuery);
