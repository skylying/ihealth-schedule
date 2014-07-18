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
		window.filterInstituteOnChange = function()
		{
			return function(e, $node)
			{
				$node.closest('form').submit();
			};
		};
	}

	if (window.filterMemberOnChange === undefined)
	{
		/**
		 * On change event of filter "member_id"
		 *
		 * @param {object} e
		 * @param {jQuery} $node
		 */
		window.filterMemberOnChange = function()
		{
			return function(e, $node)
			{
				$node.closest('form').submit();
			};
		};
	}

	if (window.closeModal === undefined)
	{
		window.closeModal = function(id)
		{
			$(id).modal('hide');
		};
	}

	if (window.bindDateFilter === undefined)
	{
		window.bindDateFilter = function($node)
		{
			$node.on('dp.change', function()
			{
				$('#adminForm').submit();
			});
		};
	}

	/**
	 * Bind "onClick" events on status dropdown menu
	 *
	 * @returns void
	 */
	function bindStatusDropdownMenu()
	{
		var $adminForm = $('#adminForm'),
			$cancelForm = $('#form-status-cancel'),
			$pauseForm = $('#form-status-pause'),
			$modalStatusCancel = $('#modal-status-cancel'),
			$modalStatusPause = $('#modal-status-pause'),
			$adminStatusInput = $adminForm.find('input[name="status"]'),
			$cancelStatusInput = $cancelForm.find('input[name="status"]'),
			$cancelCidInput = $cancelForm.find('input[name="cid[]"]'),
			$pauseCidInput = $pauseForm.find('input[name="cid[]"]'),
			$cancelNoteTextArea = $cancelForm.find('textarea[name="cancel_note"]'),
			$pauseNoteTextArea = $pauseForm.find('textarea[name="cancel_note"]');

		$('.status-dropdown-menu li').click(function()
		{
			var $node = $(this),
				index = $node.data('index'),
				$cid = $('#cb' + index),
				$statusBtn = $('#btn-status-' + index),
				status = $node.data('status'),
				updateMethod = $node.data('update-method'),
				cancelNote = $statusBtn.data('default-cancel-note'),
				cancelReason = $statusBtn.data('default-cancel');

			switch (updateMethod)
			{
				case 'submit':
					$cid.click();
					$adminStatusInput.val(status);
					window.Joomla.submitbutton('schedules.update.status');
					break;
				case 'modal-cancel':
					$cancelStatusInput.val(status);
					$cancelCidInput.val($cid.val());
					$modalStatusCancel.modal();
					$cancelNoteTextArea.text(cancelNote);
					$cancelForm.find('input[value="' + cancelReason + '"]').prop('checked', true);
					break;
				case 'modal-pause':
					$pauseCidInput.val($cid.val());
					$modalStatusPause.modal();
					$pauseNoteTextArea.text(cancelNote);
					$pauseForm.find('input[value="' + cancelReason + '"]').prop('checked', true);
					break;
			}
		});
	}

	/**
	 * Bind "onClick" event on edit submit button
	 *
	 * @returns void
	 */
	function bindEditSubmitButton()
	{
		var $adminForm = $('#adminForm'),
			$container = $('#modal-edit-item'),
			$dateInput = $container.find('input[name="date"]'),
			$senderIdSelect = $container.find('select[name="sender_id"]'),
			$adminDateInput = $adminForm.find('input[name="new_date"]'),
			$adminSenderIdInput = $adminForm.find('input[name="new_sender_id"]');

		$('#modal-edit-item-submit').click(function()
		{
			$adminDateInput.val($dateInput.val());
			$adminSenderIdInput.val($senderIdSelect.val());

			window.Joomla.submitbutton('schedules.edit');
		});
	}

	/**
	 * Class SchedulesList
	 */
	window.SchedulesList = window.SchedulesList || {
		/**
		 * Edit one schedule
		 *
		 * @param {integer} index Index of table row
		 */
		edit: function(index) {
			var $fieldDate = $('#edit_item_field_date'),
				$fieldSenderId = $('#edit_item_field_sender_id');

			$('input[name="checkall-toggle"]').prop('checked', false);

			$('input[name="cid[]"]').each(function(i, val)
			{
				var $node = $(this);

				if (i === index)
				{
					$node.prop('checked', true);

					var $row = $node.closest('tr'),
						date = $.trim($row.find('.field-date').text()),
						senderId = $row.find('.field-sender-id').data('sender-id');

					// Copy date & sender_id to modal box
					$fieldDate.val(date);
					$fieldSenderId.val(senderId);
					$fieldSenderId.trigger('liszt:updated');
				}
				else
				{
					$node.prop('checked', false);
				}
			});

			$('#edit-item-button').click();
		}
	};

	$(function()
	{
		bindStatusDropdownMenu();

		bindEditSubmitButton();
	});
})(jQuery);
