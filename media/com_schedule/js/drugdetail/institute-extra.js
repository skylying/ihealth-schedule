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
	 */
	function InstituteExtra(buttonClass, removeButtonClass, rowIdPrefix)
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

		var extra = this;

		// Add button event
		$("." + this.buttonClass).click(function()
		{
			extra.addInstituteExtraRow($(this).data("instituteId"));
		});
	}

	InstituteExtra.prototype = {
		/**
		 * 刪除 row 事件
		 *
		 * @param  row jquery
		 *
		 * @return  void
		 */
		deleteRowEvent: function(row)
		{
			row.find("." + this.removeButtonClass).click(function()
			{
				row.remove();
			});
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

			// Bind event
			this.deleteRowEvent(row);

			$(rowId).after(row);
		}
	}

	window.InstituteExtra = InstituteExtra;

})(jQuery);
