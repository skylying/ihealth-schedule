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
			this.$typeField  = $('#jform_type.radio');
			this.$typeRadios = this.$typeField.find('input');
			this.$instituteControl  = $('#control_jform_institute_id');
			this.$instituteSelector = $('#jform_institute_id');

			// Register events
			this.registerEvents();

			// init selector
			this.toggleInstituteSelector(this.$typeRadios.find(':checked').val());

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
			}
			else
			{
				this.$instituteControl.hide();

				this.$instituteSelector.select2('val', '');
			}
		}
	};

})(jQuery);
