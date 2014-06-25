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
		 * Option
		 */
		option: {
			customerApi: ''
		},
		/**
		 * Run
		 *
		 * @param {Object} option
		 */
		run: function(option)
		{
			this.option = $.extend(this.option, option);

			var self = this,
				$panel = $('#rx-list').find('tbody'),
				$totalRow = $('#totalrow'),
				$instituteId = $('#jform_institute_id'),
				handler = new MultiRowHandler({$panel:$panel});

			// Bind afterInsert event
			handler.on('afterInsert', function ($row)
			{
				var customers = $instituteId.data('customers');

				$row.find('input.customer-id').each(function (i, val)
				{
					Select2Helper.select2('customer_id', $(this), {data:customers});
				});
			});

			// Bind afterDuplicate event
			handler.on('afterDuplicate', function ($row, $from)
			{
				var customers = $instituteId.data('customers'),
					customer = $from.find('input.customer-id').select2('data') || {id: '', name: ''};

				$row.find('input.customer-id').each(function (i, val)
				{
					// Remove exists select2 element
					$(this).parent().find('.select2-container').remove();

					Select2Helper.select2('customer_id', $row.find('input.customer-id'), {data:customers,initialData:customer});
				});
			});

			// Bind initialize event
			handler.on('initializeRow', function ($row)
			{
				var $dpNode = $row.find('.datetimepicker');

				$dpNode.datetimepicker({
					pickTime: false
				});

				$dpNode.on('dp.change', function(e)
				{
					// Focus on a known element to improve performance
					$('#jform_floor').focus().blur();

					self.updateFinishDate.call(this);
				});

				$dpNode.on('dp.hide', function(e)
				{
					// Trigger event "dp.change" when select today's date
					if (0 === moment().diff(e.date, 'days'))
					{
						console.log('trigger dp.change');

						$(this).trigger('dp.change');
					}
				});
			});

			// Inject javascript to every element on page
			this.injectJsToRow($panel);

			// Add row
			$('.button-add-row').click(function ()
			{
				var amount = $(this).val();

				amount = isNaN(amount) ? 0 : amount;

				for (var i = 0; i < amount; ++i)
				{
					handler.insert($('#row-template').html());
				}

				updateTotalRowNumber();
			});

			// Delete row
			$panel.on('click', '.button-delete-row', function ()
			{
				handler.remove($(this).closest('tr'));

				updateTotalRowNumber();
			});

			// Copy row
			$panel.on('click', '.button-copy-row', function ()
			{
				handler.duplicate($(this).closest('tr'));

				updateTotalRowNumber();
			});

			// Update total row number first
			updateTotalRowNumber();

			/**
			 * Update total number of rows
			 */
			function updateTotalRowNumber()
			{
				$totalRow.text($panel.find('tr').length);
			}
		},

		/**
		 * Triggered by institute_id onchange event, will update "外送日", "機構樓層", "顏色", and Customer Select2 Data
		 *
		 * It will return a callback function, which contains params:
		 * - {object}  e     Event object, Contains the following custom properties:
		 *                        - val:     The current selection (taking into account the result of the change) - id or array of ids.
		 *                        - added:   The added element, if any - the full element object, not just the id.
		 *                        - removed: The removed element, if any - the full element object, not just the id.
		 * - {element} $node Current Element node
		 */
		instituteIdChange: function()
		{
			var self = this;

			return function(e, $node)
			{
				var translateWeek = {
					MON: '週一',
					TUE: '週二',
					WED: '週三',
					THU: '週四',
					FRI: '週五',
					SAT: '週六',
					SUN: '週日'
				};

				var weekday = e.added.delivery_weekday,
					color = e.added.color_hex,
					floor = e.added.id.split('-')[1],
					customerApiUrl = self.option.customerApi + e.val.split('-')[0];

				// update delivery_weekday
				$('#weekday-from-js').text(translateWeek[weekday]);

				// update delivery_weekday color
				$('.deliverycolor').css('background', color);

				// update floor
				$('#jform_floor').val(floor);

				$.getJSON(customerApiUrl, function(obj)
				{
					$node.data('customers', obj);

					$('input.customer-id').each(function(i, val)
					{
						var $self = $(this),
							data = $self.select2('data'),
							select2Option = $self.data('select2');

						select2Option.opts.data.results = obj;
						select2Option.opts.initSelection = function(element, callback)
						{
							callback(data);
						};

						// Clear selected id
						if (data && !isNaN(data.id))
						{
							$self.val('');
						}

						$self.select2(select2Option.opts).change();
					});
				});
			}
		},

		/**
		 * Triggered by customer_id onchange event, will update "身分證字號" and "生日"
		 *
		 * It will return a callback function, which contains params:
		 * - {object}  e     Event object, Contains the following custom properties:
		 *                        - val:     The current selection (taking into account the result of the change) - id or array of ids.
		 *                        - added:   The added element, if any - the full element object, not just the id.
		 *                        - removed: The removed element, if any - the full element object, not just the id.
		 * - {element} $node Current Element node
		 */
		customerIdChange: function()
		{
			return function(e, $node)
			{
				var $idNumber = $node.closest('tr').find('.idnumber'),
					$birthDay = $node.closest('tr').find('.birthday');

				if (typeof e.added === 'object')
				{
					$idNumber.val(e.added.id_number);
					$birthDay.val(e.added.birth_date);
				}
				else
				{
					$idNumber.val('');
					$birthDay.val('');
				}
			};
		},

		// Inject javascript to every single row
		injectJsToRow : function($panel)
		{
			// 可調劑次數與處方箋外送次數連動
			$panel.on('change', '.times', function()
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
			$panel.on('change', '.times', function(event)
			{
				var $row = $($(this).closest('tr')),
					timesValue = $(event.target).val(),
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
			$panel.on('change', '.period', this.updateFinishDate);
		},

		/**
		 * Update "第一次吃完藥品日" & "第二次吃完藥品日"
		 *
		 * @param event
		 *
		 * @return void
		 */
		updateFinishDate: function()
		{
			var $row = $(this).closest('tr');

			var seeDrDateNode  = $row.find('.seedr'),
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
	};
})(jQuery);
