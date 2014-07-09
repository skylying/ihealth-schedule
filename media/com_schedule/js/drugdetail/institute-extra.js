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

		// Bind all event we need
		this.bindEvent();
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
			});

			$(rowId).after(row);
		}
	};

	// Export object
	window.InstituteExtra = InstituteExtra;
})(jQuery);
