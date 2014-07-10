;(function($)
{
	// Check global conflict
	if (typeof window.RouteJs !== "undefined")
	{
		return;
	}

	"use strict";

	// Register RouteJs
	window.RouteJs = {

		// Prepare all element we need
		initialize : function()
		{
			this.routeBlocks = $('.routeinput');
			this.hiddenArea  = $('#hidden-inputs');

			// Uncheckall button in <table> scope
			this.uncheckalltable = $('#uncheckalltable');

			// check all button in single <td> scope
			this.checkall   = $('.checkall');
			this.uncheckall = $('.uncheckall');

			// Checkall button's mask
			this.mask = $('.mask');

			this.setMask();

			// Bind all events we need
			this.bindEvent();
		},

		// If there are too few routes in single <td>, disable hover effect
		setMask : function()
		{
			this.mask.each(function()
			{
				var checkboxes = $(this).closest('td').find('input[type="checkbox"]'),
					maskSwitch = 3;

				if (checkboxes.length <= maskSwitch)
				{
					$(this).css('z-index', '-99999');
				}
			});
		},

		/**
		 * Bind all HTML events we need
		 */
		bindEvent : function()
		{
			// Register self as RouteJs object alias
			var self = this;

			// Bind onchange event to each routeBlock checkbox
			this.routeBlocks.on('change', function()
			{
				var routeId = $(this).attr('id');

				// Create new input for each route
				if (this.checked)
				{
					var inputHtml = self.createInputElement($(this));

					// Emphasize selected target
					$(this).closest('.route-outer').css('opacity', '1');

					// Append route hidden inputs
					self.hiddenArea.append(inputHtml);
				}
				// If user uncheck route, remove input to be sent
				else
				{
					$(this).closest('.route-outer').css('opacity', '0.7');

					self.hiddenArea.find('input[title="' + routeId + '"]').remove();
				}
			});

			// Bind uncheckall button event
			this.uncheckalltable.on('click', function()
			{
				var allInputs = $('.routeinput');

				allInputs.each(function()
				{
					$(this).prop('checked', false);

					// execute routeBlocks onchange event
					self.routeBlocks.trigger('change');
				})
			});

			// Bind checkall button event
			this.mask.hover(

				// Hover in effect, 目前設計不考慮 hover out, 所以用 javascript 跑
				function()
				{
					$(this).css('opacity', '1');
					$(this).css('position', 'relative');
					$(this).find('.checkall, .uncheckall').css('display', 'inline-block');
				});

			// Bind checkall button event
			this.checkall.on('click', function()
			{
				// Find all checkboxes in current <td>
				var checkboxes = $(this).closest('td').find('input[type="checkbox"]');

				// Prevent duplicated hidden inputs
				$(checkboxes).prop('checked', false);
				self.routeBlocks.trigger('change');

				// Execute routeBlocks onchange event
				$(checkboxes).prop('checked', true);
				self.routeBlocks.trigger('change');
			});

			// Bind checkall button event
			this.uncheckall.on('click', function()
			{
				// Find all checkboxes in current <td>
				var checkboxes = $(this).closest('td').find('input[type="checkbox"]');

				$(checkboxes).prop('checked', false);

				// Execute routeBlocks onchange event
				self.routeBlocks.trigger('change');
			});
		},

		/**
		 * Create <input> with value loaded from php
		 *
		 * @param {object} checkbox
		 *
		 * @return HTML object
		 */
		createInputElement : function(checkbox)
		{
			// Get input attributes
			var attributes = this.configureInput(checkbox);

			return $('<input/>', {
				'id'    : attributes.elementId,
				'type'  : attributes.type,
				'name'  : attributes.name,
				'title' : attributes.title,
				'class' : attributes.class,
				'value' : attributes.value.id
			});
		},

		/**
		 * Generate each unique input attributes
		 *
		 * @param {object} checkbox
		 *
		 * @returns object
		 */
		configureInput : function(checkbox)
		{
			var routeId       = checkbox.attr('id'),
				routeValueObj = $.parseJSON(checkbox.val());

			var date = new Date,
				timeStamp = date.getTime(),
				inputConfig = {};
			inputConfig.value = {};

			// Give each dynamically created input an unique id
			inputConfig.elementId = 'date-' + timeStamp;
			inputConfig.type      = 'hidden';
			inputConfig.name      = 'cid[]';
			inputConfig.title     = routeId;
			inputConfig.class     = 'hidden-route-inputs';

			// Put route value back
			inputConfig.value.id           = routeId;
			inputConfig.value.type         = routeValueObj.type;
			inputConfig.value.institute_id = routeValueObj.institute_id;

			return inputConfig;
		}
	};
})(jQuery);