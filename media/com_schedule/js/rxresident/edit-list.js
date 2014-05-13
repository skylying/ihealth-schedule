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

			// Bind afterInsert event
			handler.on('afterInsert', function ($row)
			{
				$row.find('input.customer-id').each(function()
				{
					customer_id.initialize($(this));
				});
			});

			// Bind afterDuplicate event
			handler.on('afterDuplicate', function ($row)
			{
				// Remove exists select2 element
				$row.find('div.customer-id').remove();

				$row.find('input.customer-id').each(function ()
				{
					customer_id.initialize($(this));
				});
			});

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
				});

				// if "可調劑次數" = 1, hide "第二次藥品吃完日"
				$row.find('.times').on('change', function(event)
				{
					var timesValue = $(event.target).val(),
						finishDate2Span = $row.find('.emptydisplay2');

					if (timesValue == '1')
					{
						finishDate2Span.addClass('hide');
					}
					else
					{
						finishDate2Span.removeClass('hide');
					}

				});

				// Bind onchange to "就醫日期" & "給藥天數"
				$row.find('.seedr').on('change', {row:$row}, updateFinishDate);
				$row.find('.period').on('change', {row:$row}, updateFinishDate);

				/**
				 * Update "第一次吃完藥品日" & "第二次吃完藥品日"
				 *
				 * @param event
				 *
				 * @return void
				 */
				function updateFinishDate(event)
				{
					var $row = event.data.row;

					var seeDrDateNode  = $row.find('.seedr input'),
						seeDrDateValue = seeDrDateNode.val();

					// if "就醫日期" not set, return
					if (seeDrDateValue == 'undefined' || seeDrDateValue == '')
					{
						return;
					}

					var periodNode      = $row.find('.period'),
						periodValue     = parseInt(periodNode.val());

					var dateObj = new Date(seeDrDateValue);

					// Set finishdate1 & finishdate2
					var finishDate1 = dateObj.setDate(dateObj.getDate() + periodValue),
						finishDate2 = dateObj.setDate(dateObj.getDate() + periodValue);

					// update "藥吃完日" view column
					$row.find('.emptydisplay1').text(getFormat(finishDate1, false));
					$row.find('.emptydisplay2').text(getFormat(finishDate2, false));

					// update "藥吃完日" form field
					$row.find('.emptydate1').val(getFormat(finishDate1, true));
					$row.find('.emptydate2').val(getFormat(finishDate2, true));

					/**
					 * Create date format MM-DD
					 *
					 * @param   {timestamp} date
					 * @param   {boolean}   type , true => return full format
					 *
					 * @returns {string}
					 */
					function getFormat(date, type)
					{
						var dateObj = new Date(date),
							year    = dateObj.getFullYear(),
							month   = dateObj.getMonth() + 1,
							day     = dateObj.getDate(),
							short   = ((month < 10) ? '0'+ month : month) + '-' + ((day < 10) ? '0'+ day : day),
							full    = year + '-' + short,
							result;

						if (type == true)
						{
							result = full;
						}
						else
						{
							result = short;
						}

						return result;
					}
				}


				// Activate calendar by click input itself
				$row.find('.datetimepicker').each(function()
				{
					var calendarIcon = $(this).find('.input-group-addon');

					$(this).find('input').on('click', function()
					{
						calendarIcon.click();
					})
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

	/**
	 * Bind onchange handler to update "外送日" column
	 * Event Triggerer : rxresident.xml => Fieldname = "institute_id"
	 *
	 * @param {object}  e
	 * @param {element} $node
	 */
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

		// Export institute_id
		window.instituteId = e.added.id;

		// If user change institute, initialize $row with select2 again
		var panel = $('#rx-list').find('tr');

		$(panel).each(function()
		{
			// TODO:customer_id 目前寫死
			customer_id.initialize($(this).find('.customer-id'));
		})

	};

	/**
	 * Bind onchahge handler to update customer id_number and birth_date
	 * Event Triggerer : rxresident.xml => Fieldname = "customer_id"
	 *
	 * @param {object}  e
	 * @param {element} $node
	 */
	window.updateIdBirthday = function(e, $node)
	{
		var idNumber = $node.closest('tr').find('.idnumber'),
			birthDay = $node.closest('tr').find('.birthday input');

		idNumber.val(e.added.id_number);
		birthDay.val(e.added.birth_date);
	};

})(jQuery);
