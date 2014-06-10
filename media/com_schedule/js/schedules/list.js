;(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.filterInstituteOnChange === undefined)
	{
		/**
		 * On change event of filter "institute_id"
		 *
		 * @param {object} e
		 * @param {jQuery} $node
		 */
		window.filterInstituteOnChange = function(e, $node)
		{
			// Replace $node value with real institute id
			if (e.added.instituteid)
			{
				$node.val(e.added.instituteid);
			}

			$node.closest('form').submit();
		};
	}
})(jQuery);
