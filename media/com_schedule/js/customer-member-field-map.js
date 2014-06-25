;(function ($)
{
	"use strict";

	// Prevent global conflict
	if (typeof window.customerMemberField != 'undefined')
	{
		return;
	}

	window.customerMemberField = {
		init: function($customerId, $memberSelectId, $url)
		{
			this.customer = $("#" + $customerId);
			this.member   = $("#" + $memberSelectId);
			this.memberId = $memberSelectId;
			this.url      = $url;

			// Field init
			this.flushSelect();

			// Event init
			this.customer.change(function()
			{
				window.customerMemberField.flushSelect();
			});
		},
		flushSelect: function()
		{
			var memberId    = this.memberId;

			// Reset element
			this.member     = $("#" + memberId);

			var memberField = this.member;
			var customerVal = this.customer.val();
			var ajaxUrl     = this.url + '&customer_id=' + customerVal;

			$.getJSON(ajaxUrl, function(data)
			{
				var html = '';
				var field = window.customerMemberField;

				$.each(data, function(){
					html = html + field.selectListHtml(this.name, this.id);
				});

				html = field.selectHtml(html);

				memberField.replaceWith(html);
			});
		},
		selectHtml: function($listHtml)
		{
			var $id    = this.member.attr("id");
			var $name  = this.member.attr("name");
			var $class = this.member.attr("class");

			return "<select id='" + $id + "' name='" + $name + "' class='" + $class + "'>" + $listHtml + "</select>";
		},
		selectListHtml: function($title, $value)
		{
			return "<option value='" + $value + "'>" + $title + "</option>";
		}
	}
})(jQuery);
