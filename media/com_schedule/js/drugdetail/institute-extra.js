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
	 * @param buttonClass  string  button class name
	 * @param rowIdPrefix  string  row id prefix
	 */
	function InstituteExtra(buttonClass, $rowIdPrefix)
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
		this.rowIdPrefix = $rowIdPrefix;

		this.addInstituteExtraButtonEvent();
	}

	InstituteExtra.prototype = {
		/**
		 * 新增機構額外表按鈕事件
		 *
		 * @return  void
		 */
		addInstituteExtraButtonEvent: function()
		{
			var extra = this;

			$("body").delegate("." + this.buttonClass, "click", function()
			{
				extra.addInstituteExtraRow($(this).data("instituteId"));
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

				$(this).attr("name", fieldName);
				$(this).attr("id", fieldId);
			});

			$(rowId).after(row);
		}
	}

	window.InstituteExtra = InstituteExtra;

})(jQuery);