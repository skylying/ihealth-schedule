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
			this.td         = $('.calendar tr td');
			this.inputsArea = $('#hidden-inputs-area');

			// Global namespace for "this"
			window.$this = this;

			// Bind all event we need
			$this.bindEvent();
		},

		// Bind all event
		bindEvent : function()
		{
			$this.td.on('click', function()
			{
				var valuePackage = {};

				valuePackage.date      = $(this).attr('data-date');
				valuePackage.holidayId = $(this).attr('id');
				valuePackage.state     = '1';

				var weekDay = new Date(valuePackage.date).getDay();

				// When user click empty date, return
				if (valuePackage.date == '' || valuePackage.date == undefined)
				{
					return false;
				}

				// When user click weekend, return
				if (weekDay == 0 || weekDay == 6)
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

				$this.createInput(valuePackage);
			})
		},

		/**
		 * Create an input by each date click
		 *
		 * @param {object} valuePackage
		 */
		createInput : function(valuePackage)
		{
			var inputConfig = $this.configureNewInput(valuePackage);

			var input = $('<input/>', {
				'id'    : inputConfig.elementId,
				'name'  : 'jform[date][]',
				'value' : JSON.stringify(inputConfig.value)
			});

			$this.inputsArea.append(input);
		},

		/**
		 * Generate input value and attributes
		 *
		 * @param {object} valuePackage
		 *
		 * @returns object
		 */
		configureNewInput : function(valuePackage)
		{
			var date = new Date,
				timeStamp = date.getTime(),
				inputConfig = {};
				inputConfig.value = {};

			// Give each dynamically created input an unique id
			inputConfig.elementId = 'date-' + timeStamp;

			inputConfig.value.date      = valuePackage.date;
			inputConfig.value.holidayId = valuePackage.holidayId;
			inputConfig.value.state     = valuePackage.state;

			return inputConfig;
		}
	}
})(jQuery);
