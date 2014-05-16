;(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.RouteEdit !== undefined)
	{
		return;
	}

	// Exports class RouteEdit
	window.RouteEdit = {
		/**
		 * Run
		 */
		run: function(options)
		{
			options = $.extend({
				isEdit: false
			}, options);

			function updateInstituteNode(condition)
			{
				var $node = $('#control_jform_institute_id');

				if ('institute' === condition)
				{
					if ($node.hasClass('hide'))
					{
						$node.removeClass('hide');
					}
				}
				else
				{
					if (! $node.hasClass('hide'))
					{
						$node.addClass('hide');

						$('#jform_institute_id').select2('val', '');
					}
				}
			}

			$('input[name="jform[type]"]').click(function()
			{
				updateInstituteNode($(this).val());
			});

			updateInstituteNode($('input[name="jform[type]"]:checked').val());

			if (options.isEdit)
			{
				// Disable "type" radio box
				$('#jform_type').find('.btn').attr('disabled', true);
			}
		}
	};

})(jQuery);
