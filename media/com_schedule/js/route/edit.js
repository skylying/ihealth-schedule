;(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.RouteEdit !== undefined)
	{
		return;
	}

	/**
	 * The Route edit control object.
	 *
	 * @since  1.0
	 */
	window.RouteEdit = {
		/**
		 * Run
		 */
		run: function(options)
		{
			options = $.extend({
				isEdit: false
			}, options);

			// Prepare element cache
			this.$typeField  = $('#jform_type');
			this.$typeRadios = this.$typeField.find('input');
			this.$instituteControl  = $('#control_jform_institute_id');
			this.$instituteSelector = $('#jform_institute_id');
			this.$city = $('#jform_city');
			this.$area = $('#jform_area');

			// Register events
			this.registerEvents();

			// Hide institute_id selection when route type is customer
			if ('institute' !== this.$typeField.find('input:checked').val())
			{
				this.$instituteControl.hide();
			}

			// Disable some fields in edit page
			if (options.isEdit)
			{
				// Disable "type" radio box
				this.$typeField.find('.btn').attr('disabled', true);
			}
		},

		/**
		 * Register events.
		 *
		 * @return  void
		 */
		registerEvents: function()
		{
			var self = this;

			// Toggle institute selector show or hide.
			this.$typeRadios.click(function()
			{
				self.toggleInstituteSelector($(this).val());
			});
		},

		/**
		 * Toggle institute selector show or hide.
		 *
		 * @param   {string}  condition  The string of route type.
		 *
		 * @return  void
		 */
		toggleInstituteSelector: function(condition)
		{
			if ('institute' === condition)
			{
				this.$instituteControl.show();

				this.$city.attr('disabled', true);
				this.$area.attr('disabled', true);
			}
			else
			{
				this.$instituteControl.hide();

				this.$instituteSelector.select2('val', '');

				this.$city.attr('disabled', false);
				this.$area.attr('disabled', false);
			}

			this.$city.val('');
			this.$city.change();
			this.$city.trigger('liszt:updated');

			this.$area.val('');
			this.$area.trigger('liszt:updated');
		},

		/**
		 * Triggered by institute_id onchange event, will update "縣市", "區域"
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

				self.$city.val(data.city);
				self.$city.change();
				self.$city.trigger('liszt:updated');

				self.$area.val(data.area);
				self.$area.trigger('liszt:updated');
			}
		}
	};

})(jQuery);
