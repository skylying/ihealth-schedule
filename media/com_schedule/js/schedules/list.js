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

	if (window.closeModal === undefined)
	{
		window.closeModal = function(id)
		{
			$(id).modal('hide');
		}
	}

	$(function()
	{
		var $adminForm = $('#adminForm'),
			$cancelForm = $('#form-status-cancel'),
			$pauseForm = $('#form-status-pause'),
			$modalStatusCancel = $('#modal-status-cancel'),
			$modalStatusPause = $('#modal-status-pause'),
			$adminStatusInput = $adminForm.find('input[name="status"]'),
			$cancelStatusInput = $cancelForm.find('input[name="status"]'),
			$cancelCidInput = $cancelForm.find('input[name="cid[]"]'),
			$pauseCidInput = $pauseForm.find('input[name="cid[]"]');

		// Bind "onClick" events on status dropdown menu
		$('.status-dropdown-menu li').click(function()
		{
			var $node = $(this),
				index = $node.data('index'),
				$cid = $('#cb' + index),
				status = $node.data('status'),
				updateMethod = $node.data('update-method');

			switch (updateMethod)
			{
				case 'submit':
					$cid.click();
					$adminStatusInput.val(status);
					window.Joomla.submitbutton('schedules.status');
					break;
				case 'modal-cancel':
					$cancelStatusInput.val(status);
					$cancelCidInput.val($cid.val());
					$modalStatusCancel.modal();
					break;
				case 'modal-pause':
					$pauseCidInput.val($cid.val());
					$modalStatusPause.modal();
					break;
			}
		});
	});
})(jQuery);
