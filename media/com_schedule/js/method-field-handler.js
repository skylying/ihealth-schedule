;
(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.MethodFieldHandler !== undefined)
	{
		return;
	}

	window.MethodFieldHandler = {
		setOptions: function(options)
		{
			// Overwrite with user's options
			this.options = $.extend(true, {
				methodId     : null,
				drugId       : null,
				deleteDrugId : null
			}, options);
		},

		run: function()
		{
			var self = this;

			var methodElement = $('#' + self.options.methodId);
			// Find where to store hicodes
			var targetHiddenInput = $('#' + self.options.drugId);

			// Update while hicode date already exist on the very first time
			if (targetHiddenInput.val() != '' && targetHiddenInput.val() != '{}' && targetHiddenInput.val() != '[]')
			{
				self.insertHicodeTableRow(JSON.parse(targetHiddenInput.val()));
			}

			// Copy the HiCode template.
			var tableTmpl = $('.js-hicode-tmpl');

			// Append tabel after method select list
			tableTmpl.insertAfter(methodElement.closest('.control-group'));

			// Bind Prescription method change detection
			methodElement.on('change', function()
			{
				if ($(this).val() == 'form')
				{
					// Update from input
					self.updateHicodeHiddenInput();
					// Show table
					tableTmpl.removeClass('hide');
				}
				else
				{
					targetHiddenInput.val('');
					tableTmpl.addClass('hide');
				}
			});

			// Trigger once
			methodElement.trigger('change');

			// Combine two selector.
			var hicodeElement = $('.js-hicode-code');
			var quantityElement = $('.js-hicode-quantity');
			var combinedHicodeElem = hicodeElement.add(quantityElement);

			// Every time when 'hicode' and 'quantity' being changed.
			tableTmpl.on('change', combinedHicodeElem, function()
			{
				self.updateHicodeHiddenInput();
			});

			// Bind Add event
			$('.js-hicode-add-row').on('click', function()
			{
				var cloneRow = $(".js-hicode-row").first().clone();
				// Retrieve hicode
				cloneRow.find('.js-hicode-code').val('');
				// Retrieve quantity
				cloneRow.find('.js-hicode-quantity').val('');
				// Retrieve id
				cloneRow.find('.js-hicode-id').val('');

				$('.js-hicode-tmpl tbody').append(cloneRow);
			});

			// Bind Delete event
			$('.js-hicode-tmpl').on('click', '.js-hicode-delete-row', function()
			{
				// Stores id to delete
				// ex: [1, 2]
				var data = [];
				var targetInput = $('#' + self.options.deleteDrugId);

				if (targetInput.val() && targetInput.val() != '[]')
				{
					data = JSON.parse(targetInput.val());
				}

				if (confirm('您確定要刪除嗎？'))
				{
					if ($(".js-hicode-row").size() > 1)
					{
						var idToDelete = $(this).closest('.js-hicode-row').find('.js-hicode-id').val();

						// Colect deleted Ids
						if (idToDelete)
						{
							data.push(idToDelete);
						}

						// Delete row
						$(this).closest('.js-hicode-row').remove();
						// Update Hidden Input
						self.updateHicodeHiddenInput();
					}
					else
					{
						var idToDelete = $(this).closest('.js-hicode-row').find('.js-hicode-id').val();

						// Colect deleted Ids
						if (idToDelete)
						{
							data.push(idToDelete);
						}

						var row = $(".js-hicode-row").first();
						// Retrieve hicode
						row.find('.js-hicode-code').val('');
						// Retrieve quantity
						row.find('.js-hicode-quantity').val('');
						// Retrieve id
						row.find('.js-hicode-id').val('');

						// Update Hidden Input
						self.updateHicodeHiddenInput();
					}

					// Update deleted Ids
					targetInput.val(JSON.stringify(data));
				}
			});
		},

		updateHicodeHiddenInput: function()
		{
			var self = this;
			var newRowCounter = 0;
			var totalRowCounter = 0;

			// Data to stored
			var data = [];

			// Go through every row, and push it into hidden input
			$('.js-hicode-row').each(function()
			{
				// Retrieve hicode
				var code = $(this).find('.js-hicode-code').val();
				// Retrieve quantity
				var quantity = $(this).find('.js-hicode-quantity').val();
				// Retrieve id
				var id = $(this).find('.js-hicode-id').val();

				// blank new row
				if (id == '')
				{
					// Make sure every info is provided
					if ((code != '') && (quantity != ''))
					{
						data.push({id: '', hicode: code, quantity: quantity});
						newRowCounter++;
						totalRowCounter++;
					}
				}
				// if 'create' doesn't exist, and id exist
				else
				{
					// Make sure every info is provided
					if ((code != '') && (quantity != ''))
					{
						data.push({id: id, hicode: code, quantity: quantity});
						totalRowCounter++;
					}
				}

				// Perform hidden input update
				self.updateJsonToInputField(self.options.drugId, data);

				// Update Counter
				$('.js-hicode-amount').text(totalRowCounter);
			});
		},

		insertHicodeTableRow: function(data)
		{
			var tableTbody = $('.js-hicode-tmpl tbody');
			var cloneRow = $(".js-hicode-row").first().clone();

			// Retrieve hicode
			cloneRow.find('.js-hicode-code').val('');
			// Retrieve quantity
			cloneRow.find('.js-hicode-quantity').val('');
			// Retrieve id
			cloneRow.find('.js-hicode-id').val('');

			// Clear tbody
			tableTbody.html('');

			for (var i = 0; i < data.length; i++)
			{
				var insertRow = cloneRow.clone();

				insertRow.find('.js-hicode-code').val(data[i].hicode);
				// Retrieve quantity
				insertRow.find('.js-hicode-quantity').val(data[i].quantity);
				// Retrieve id
				insertRow.find('.js-hicode-id').val(data[i].id);

				tableTbody.append(insertRow);
			}

			tableTbody.append(cloneRow);
		},

		updateJsonToInputField: function(target, dataJson)
		{
			dataJson = dataJson || {};

			var targetElement = $('#' + target);

			// Check if selector get null
			if (targetElement.length !== 0)
			{
				targetElement.val(JSON.stringify(dataJson));
			}
		}
	};
})(jQuery);
