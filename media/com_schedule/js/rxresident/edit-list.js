;(function($, undefined)
{
	"use strict";

	// Selectors
	var $instituteId,
		$instituteIdSelection,
		$floor,
		$deliveryWeekdayText,
		$deliveryWeekday,
		$note,
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
		$note = $('#note-from-js');
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
	 * 編輯處方箋時, 處方箋連動只需要更改 disabled 狀態, 不做 check, 否則會把 form data 洗掉
	 */
	function setDisabledBox()
	{
		var $node = $('.times'),
			cb1 = $panel.find('input[value="1st"]'),
			cb2 = $panel.find('input[value="2nd"]'),
			cb3 = $panel.find('input[value="3rd"]'),
			$drugEmptyDateText2 = $panel.find('.drug-empty-date-text2');

		switch ($node.find('option:selected').val())
		{
			case '1':
				cb2.attr('disabled', true);
				cb3.attr('disabled', true);

				$drugEmptyDateText2.hide();

				break;

			case '2':
				cb2.attr('disabled', false);
				cb3.attr('disabled', true);

				$drugEmptyDateText2.show();

				break;

			case '3':
				cb2.attr('disabled', false);
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
	 * Large checkbox check function
	 */
	function clickLargeLabel()
	{
		$(this).prev().click();
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
		updateDrugEmptyDate.call(this);
	}

	/**
	 * Valid date manually entered by user
	 *
	 * @param {string} date
	 * @param {object} elem
	 */
	function validateDate(date, elem)
	{
		// 10 digits like 2014-01-23, and has to be reasonable date
		var validDashedDateExp = new RegExp(/([1-2]{1}[0-9]{1}[0-9]{2}[-]{1}[0-1]{1}[0-9]{1}[-]{1}[0-3]{1}[0-9]{1})/g);

		// Date has to be reasonable 8 digits, EX : 30002359 is not allow here
		var validDateExp = new RegExp(/([1-2]{1}[0-9]{1}[0-9]{2}[0-1]{1}[0-9]{1}[0-3]{1}[0-9]{1})/g);

		var validState = {
			validDashedDate : validDashedDateExp.test(date),
			validDate : validDateExp.test(date)
		};

		var length = date.length;

		if (length == 10)
		{
			if (!validState.validDashedDate)
			{
				throw new UserException('年、月、日必須落在合理範圍');
			}
		}
		else if (length == 8)
		{
			if (!validState.validDate)
			{
				throw new UserException('年、月、日必須落在合理範圍');
			}

			// Replace formatted date "YY-MM-DD"
			$(elem).val(date.substr(0, 4) + '-' + date.substr(4, 2) + '-' + date.substr(6, 2));
		}
		else
		{
			throw new UserException('正確日期格式應為 "2014-02-01" 或是 "20140201"');
		}
	}

	/**
	 * Method to validate see doctor date and render tooltip
	 */
	function checkSeeDrDate()
	{
		var seeDrDate = $(this).val();

		try
		{
			validateDate(seeDrDate, this);
		}
		catch(e)
		{
			var $node = $(this);

			$node.tooltip({
				trigger : "manual",
				title : e.message,
				placement : "top"
			}).tooltip('show');

			// Destroy tooltip after 3 seconds
			setTimeout(function()
			{
				$node.tooltip('destroy');
			}, 3000);
		}

		updateDrugEmptyDate.call(this);
	}

	/**
	 * Exception object
	 *
	 * @param message
	 * @constructor
	 */
	function UserException(message)
	{
		this.message = message;
		this.name = "UserException";
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

		// 檢查就醫日期
		$panel.find('.see-dr-date').focusout(checkSeeDrDate);

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
			customerApi: '',
			isEdit: true
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

			setDisabledBox();

			this.option = $.extend(this.option, option);

			var handler = new MultiRowHandler({$panel:$panel});
			var keyEventHandler = new KeyEventHandler({$panel:$panel});

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
				// 檢查就醫日期
				$row.find('.see-dr-date').focusout(checkSeeDrDate);

				// Bind onchange event to update "就醫日期" & "給藥天數"
				$row.find('.period').change(updateDrugEmptyDate);

				// 可調劑次數與處方箋外送次數連動處理
				$row.find('.times').change(timesChange);

				// large checkbox click event
				$row.find('.large-checkbox-fieldset label').click(clickLargeLabel);
			});

			// Add row
			$('#adminForm').on('click', '.button-add-row', function()
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
					note = data.note,
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

				// update note
				$note.text(note);

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
					$customerId = $row.find('.customer-id'),
					data = $node.select2('data');

				if (data)
				{
					if (data.id)
					{
						$customerId.val(data.id);
					}
				}
				else
				{
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
