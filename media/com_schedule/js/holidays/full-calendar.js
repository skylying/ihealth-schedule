;(function($)
{
	"use strict";

	// Prevent global conflict
	if (typeof window.fullCalendarJs !== "undefined")
	{
		return;
	}

	window.fullCalendarJs = {

		// Initialize all element we need
		initialize : function()
		{
			this.td         = $('.selectable');
			this.inputsArea = $('#hidden-inputs-area');

			this.hiddenInputIdList = [];

			// Bind all event we need
			this.bindEvent();
		},

		// Bind all event
		bindEvent : function()
		{
			var self = this;

			self.td.on('click', function()
			{
				var valuePackage = {};

				valuePackage.date      = $(this).data('date');
				valuePackage.holidayId = $(this).attr('id');
				valuePackage.state     = '1';

				var weekDay = new Date(valuePackage.date).getDay();

				// When user click empty date, return
				if (valuePackage.date == '' || valuePackage.date == undefined)
				{
					return false;
				}

				// 取消假日不做 database 的 delete, 改用 state 控制
				if ($(this).attr('class').indexOf('off') != -1)
				{
					valuePackage.state = '0';
					$(this).removeClass('off');
				}
				else
				{
					$(this).addClass('off');
				}

				// If check hidden input list, if exist, update it
				if (-1 == $.inArray(valuePackage.date, self.hiddenInputIdList))
				{
					self.createInput(valuePackage);
				}
				else
				{
					self.updateHiddenInput(valuePackage);
				}
			})
		},

		/**
		 * Update existing hidden input
		 *
		 * @param {object} valuePackage
		 */
		updateHiddenInput : function(valuePackage)
		{
			$('#' + valuePackage.date).val(JSON.stringify(valuePackage));
		},

		/**
		 * Create an input by each date click
		 *
		 * @param {object} valuePackage
		 */
		createInput : function(valuePackage)
		{
			var input = $('<input/>', {
				'id'    : valuePackage.date,
				'name'  : 'jform[date][]',
				'value' : JSON.stringify(valuePackage)
			});

			this.inputsArea.append(input);

			this.hiddenInputIdList.push(valuePackage.date);
		}
	}
})(jQuery);
