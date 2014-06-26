;(function($, undefined)
{
	"use strict";

	// Selectors
	var $instituteId,
		$instituteIdSelection,
		$floor,
		$deliveryWeekdayText,
		$deliveryWeekday,
		$colorText,
		$color,
		$panel,
		$totalRow,
		$rowTemplate;

	/* Globals: document, window, console */

	/**
	 * initSelectors
	 */
	function initSelectors()
	{
		$instituteId = $('#jform_institute_id');
		$instituteIdSelection = $('#jform_institute_id_selection');
		$panel = $('#rx-list').find('tbody');
		$totalRow = $('#total-row');
		$floor = $('#jform_floor');
		$rowTemplate = $('#row-template');
		$deliveryWeekdayText = $('#weekday-from-js');
		$deliveryWeekday = $('#jform_delivery_weekday');
		$colorText = $('.delivery-color');
		$color = $('#jform_color_hex');
	}

	/**
	 * Update "第一次吃完藥品日" & "第二次吃完藥品日"
	 *
	 * @returns void
	 *
	 * @private
	 */
	function updateDrugEmptyDate()
	{
		var $row = $(this).closest('tr'),
			seeDrDate = $row.find('.see-dr-date').val(),
			period = parseInt($row.find('.period').val());

		// If "就醫日期" not set, return
		if (undefined === seeDrDate || '' === seeDrDate)
		{
			return;
		}

		// Calculate "第一次吃完藥品日" & "第二次吃完藥品日"
		var drugEmptyDate1 = moment(seeDrDate).add('days', period),
			drugEmptyDate2 = moment(seeDrDate).add('days', period * 2);

		// update "藥吃完日" view column
		$row.find('.drug-empty-date-text1').text(drugEmptyDate1.format('MM-DD'));
		$row.find('.drug-empty-date-text2').text(drugEmptyDate2.format('MM-DD'));

		// update "藥吃完日" form field
		$row.find('.drug-empty-date1').val(drugEmptyDate1.format('YYYY-MM-DD'));
		$row.find('.drug-empty-date2').val(drugEmptyDate2.format('YYYY-MM-DD'));
	}

	/**
	 * Trigger onchange event to update "times (可調劑次數)" and "drug-empty-date (藥吃完日)" elements
	 *
	 * @returns void
	 *
	 * @private
	 */
	function timesChange()
	{
		var $node = $(this),
			$row = $node.closest('tr'),
			cb1 = $row.find('input[value="1st"]'),
			cb2 = $row.find('input[value="2nd"]'),
			cb3 = $row.find('input[value="3rd"]'),
			$drugEmptyDateText2 = $row.find('.drug-empty-date-text2');

		// Update deliver-nths checkboxes
		switch ($node.find('option:selected').val())
		{
			case '1':
				cb1.attr('checked', true);

				cb2.attr('checked', false);
				cb2.attr('disabled', true);

				cb3.attr('checked', false);
				cb3.attr('disabled', true);

				$drugEmptyDateText2.hide();

				break;

			case '2':
				cb1.attr('checked', false);

				cb2.attr('checked', true);
				cb2.attr('disabled', false);

				cb3.attr('checked', false);
				cb3.attr('disabled', true);

				$drugEmptyDateText2.show();

				break;

			case '3':
				cb1.attr('checked', false);

				cb2.attr('checked', true);
				cb2.attr('disabled', false);

				cb3.attr('checked', true);
				cb3.attr('disabled', false);

				$drugEmptyDateText2.show();

				break;
		}
	}

	/**
	 * Update total number of rows
	 */
	function updateTotalRowNumber()
	{
		$totalRow.text($panel.find('tr').length);
	}

	/**
	 * bindSeeDrDateEvent
	 *
	 * @param {jQuery} $node DatetimePicker element
	 *
	 * @returns void
	 */
	function bindSeeDrDateEvent($node)
	{
		function change()
		{
			// Focus out DatetimePicker element
			$node.closest('tr').find('.id-number').focus().blur();

			updateDrugEmptyDate.call(this);
		}

		$node.on('dp.change', function(e)
		{
			change.call(this);
		});

		$node.on('dp.hide', function(e)
		{
			// Trigger event "dp.change" when select today's date
			if (0 === moment().diff(e.date, 'days'))
			{
				change.call(this);
			}
		});
	}

	/**
	 * Bind element events
	 *
	 * @returns void
	 *
	 * @private
	 */
	function bindEvents()
	{
		// Bind onchange event to update "就醫日期" & "給藥天數"
		$panel.find('.period').change(updateDrugEmptyDate);

		// 可調劑次數與處方箋外送次數連動處理
		$panel.find('.times').change(timesChange);

		$('.see-dr-date').each(function()
		{
			bindSeeDrDateEvent($(this));
		});

		// Bind form submit event
		Joomla.submitbutton = function(task)
		{
			// Clear empty row before submit form
			$panel.find('tr').each(function()
			{
				var $row = $(this);

				if ('' === $row.find('.customer-id').val())
				{
					$row.remove();
				}
			});

			Joomla.submitform(task, document.getElementById('adminForm'));
		};
	}

	// Exports class RxResidentEditList
	window.RxResidentEditList = window.RxResidentEditList || {
		/**
		 * Option
		 *
		 * @type {Object}
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
			initSelectors();
			bindEvents();

			this.option = $.extend(this.option, option);

			var self = this,
				handler = new MultiRowHandler({$panel:$panel});

			// Bind afterInsert event
			handler.on('afterInsert', function($row)
			{
				var customers = $instituteIdSelection.data('customers');

				$row.find('input.customer-id-selection').each(function(i, val)
				{
					Select2Helper.select2('customer_id_selection', $(this), {data:customers});
				});
			});

			// Bind afterDuplicate event
			handler.on('afterDuplicate', function($row, $from)
			{
				var customers = $instituteIdSelection.data('customers'),
					customer = $from.find('input.customer-id-selection').select2('data') || {id: '', name: ''};

				$row.find('input.customer-id-selection').each(function(i, val)
				{
					// Remove exists select2 element
					$(this).parent().find('.select2-container').remove();

					Select2Helper.select2(
						'customer_id_selection',
						$(this),
						{
							data: customers,
							initialData: customer
						}
					);
				});
			});

			// Bind initialize event
			handler.on('initializeRow', function($row)
			{
				var $datetimePicker = $row.find('.datetimepicker');

				$datetimePicker.datetimepicker({
					pickTime: false
				});
				bindSeeDrDateEvent($datetimePicker);

				// Bind onchange event to update "就醫日期" & "給藥天數"
				$row.find('.period').change(updateDrugEmptyDate);

				// 可調劑次數與處方箋外送次數連動處理
				$row.find('.times').change(timesChange);
			});

			// Add row
			$('.button-add-row').click(function()
			{
				var amount = $(this).val();

				amount = isNaN(amount) ? 0 : amount;

				for (var i = 0; i < amount; ++i)
				{
					handler.insert($rowTemplate.html());
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

			$instituteIdSelection.change();
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
				var data = $node.select2('data');

				if (!data)
				{
					return;
				}

				var translateWeek = {
						MON: '週一',
						TUE: '週二',
						WED: '週三',
						THU: '週四',
						FRI: '週五',
						SAT: '週六',
						SUN: '週日'
					},
					weekday = data.delivery_weekday,
					color = data.color_hex,
					floor = data.id.split('-')[1],
					instituteId = data.id.split('-')[0],
					customerApiUrl = self.option.customerApi + instituteId;

				$instituteId.val(instituteId);

				// update delivery_weekday
				$deliveryWeekdayText.text(translateWeek[weekday]);
				$deliveryWeekday.val(weekday);

				// update delivery_weekday color
				$colorText.css('background', color);
				$color.val(color);

				// update floor
				$floor.val(floor);

				if (instituteId > 0)
				{
					$.getJSON(customerApiUrl, function(obj)
					{
						$node.data('customers', obj);

						$('input.customer-id-selection').each(function(i, val)
						{
							var $input = $(this),
								customerId = $input.val(),
								data = $input.select2('data') || {},
								isNewData = undefined === data['_new'] ? false : data['_new'],
								select2Option = $input.data('select2');

							select2Option.opts.data.results = obj;
							select2Option.opts.initSelection = function(element, callback)
							{
								callback(data);
							};

							// Clear selected id when data.id is not in the selection options
							if (false === obj.some(function(r) { return r.id == customerId; }) && !isNewData)
							{
								$input.val('');
							}

							$input.select2(select2Option.opts).change();
						});
					});
				}
			}
		},

		/**
		 * Triggered by customer_id_selection onchange event, will update "customer_id", "身分證字號" and "生日"
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
				var $row = $node.closest('tr'),
					$idNumber = $row.find('.id-number'),
					$birthDate = $row.find('.birth-date'),
					$customerId = $row.find('.customer-id'),
					data = $node.select2('data');

				if (data)
				{
					if (data.id)
					{
						$customerId.val(data.id);
					}

					if (data.id_number)
					{
						$idNumber.val(data.id_number);
					}

					if (data.birth_date)
					{
						$birthDate.val(data.birth_date);
					}
				}
				else
				{
					$idNumber.val('');
					$birthDate.val('');
					$customerId.val('');
				}
			};
		},

		/**
		 * bindSeeDrDateEvent
		 *
		 * @param {jQuery} $node DatetimePicker element
		 *
		 * @returns void
		 */
		bindSeeDrDateEvent: function($node)
		{
			bindSeeDrDateEvent($node);
		}
	};
})(jQuery);
