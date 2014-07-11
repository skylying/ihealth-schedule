;(function($, undefined)
{
	"use strict";

	if (window.InstituteExtra !== undefined)
	{
		return;
	}

	/**
	 * Class Institute Extra
	 *
	 * @param buttonClass        string  button class name
	 * @param removeButtonClass  string  remove button class name
	 * @param rowIdPrefix        string  row id prefix
	 * @param userId             int
	 */
	function InstituteExtra(buttonClass, removeButtonClass, rowIdPrefix, userId)
	{
		/**
		 * Button class
		 *
		 * @type  {string}
		 */
		this.buttonClass = buttonClass;

		/**
		 * Row Id Prefix
		 *
		 * @type  {string}
		 */
		this.rowIdPrefix = rowIdPrefix;

		/**
		 * Remove Button Class
		 *
		 * @type {string}
		 */
		this.removeButtonClass = removeButtonClass;

		/**
		 * User id
		 *
		 * @type {int}
		 */
		this.userId = userId;

		/**
		 * Big checkbox label
		 *
		 * @type {*|HTMLElement}
		 */
		this.bigCheckboxesLabel = $('.big-checkbox-td label');

		/**
		 * Checked big checkboxes
		 *
		 * @type {*|HTMLElement}
		 */
		this.bigCheckboxes = $('.big-checkbox:checked');

		// Bind all event we need
		this.bindEvent();

		// Set default label of checkbox style
		this.setDefaultCheck();
	}

	InstituteExtra.prototype = {

		/*
		 * Bind all events
		 */
		bindEvent : function()
		{
			// Register self as "this" alias
			var self = this;

			// New row click event
			$("." + this.buttonClass).click(function()
			{
				self.addInstituteExtraRow($(this).data("instituteId"));
			});

			// Delete row click event
			$("#schedule").on('click', '.' + self.removeButtonClass, function()
			{
				$(this).closest('tr').remove();
			});

			// Bind big checkbox label click event
			self.bigCheckboxesLabel.on('click', function()
			{
				var hasTickClass = $(this).attr('class').indexOf('tick');

				if (-1 == hasTickClass)
				{
					$(this).addClass('tick');

					$(this).closest('td .big-checkbox').prop('checked', true);
				}
				else
				{
					$(this).removeClass('tick');

					$(this).closest('td .big-checkbox').prop('checked', false);
				}
			});
		},

		/*
		 * Set default checkbox
		 */
		setDefaultCheck : function()
		{
			this.bigCheckboxes.each(function()
			{
				$(this).closest('td').find('label').addClass('tick');
			})
		},

		/**
		 * 新增機構 row
		 *
		 * @param  instituteId
		 *
		 * @return  void
		 */
		addInstituteExtraRow: function(instituteId)
		{
			var rowId = "#" + this.rowIdPrefix + instituteId;
			var row = $(rowId).clone().removeClass("hide");

			var groupTime = Date.now();

			row.find("input").each(function()
			{
				var fieldName = $(this).attr("name");
				var fieldId   = $(this).attr("id");

				fieldName = fieldName.replace("0hash0", groupTime);
				fieldId   = fieldId.replace("0hash0", groupTime);

				// Remove tmp name prefix
				fieldName = fieldName.replace("js_", "");
				fieldId   = fieldId.replace("js_", "");

				$(this).attr("name", fieldName);
				$(this).attr("id", fieldId);

				// Mark price input field
				if ($(this).attr('type') == 'text')
				{
					$(this).attr('data-type', 'extra-purchase');
				}
			});

			$(rowId).after(row);
		},

		/*
		 * Remove extra price input without actual price, will be called on form submit
		 */
		deleteEmptyPrice : function()
		{
			var extraPurchaseInputs = $('input[data-type="extra-purchase"]');

			$.each(extraPurchaseInputs, function(key, item)
			{
				if ($(item).val() == 0 || (typeof $(item).val()) == 'undefined')
				{
					$(this).closest('tr').remove();
				}
			})
		}
	};

	// Export object
	window.InstituteExtra = InstituteExtra;
})(jQuery);
