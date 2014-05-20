/**
 * MultiRowHandler Javascript Library v1.0
 */
;(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.MultiRowHandler !== undefined)
	{
		return;
	}

	/**
	 * Class MultiRowHandler
	 *
	 * Expect the format of form input name is "prefix[hash][fieldName]"
	 * For example: jform[items][10295647058132][id]
	 * The "prefix" part can be defined with "options.prefix"
	 *
	 * Events:
	 * - initializeRow    function($row)          Initialize a new row
	 *                                              $row: the new row
	 * - afterInsert      function($row)          Trigger after insert a new row
	 *                                              $row: the new row
	 * - afterDuplicate   function($row, $from)   Trigger after duplicate a new row
	 *                                              $row: the new row
	 *                                              $form: the original row
	 *
	 * @param  options            object                 Containing configuration parameters
	 * @param  options.$panel     jQuery                 Multi-Row container element
	 * @param  options.prefix     string                 Form input name prefix
	 * @param  options.insert     function($row)         Overwrite the callback to perform inserting a new row action
	 * @param  options.duplicate  function($row, $from)  Overwrite the callback to perform inserting a duplicated row action
	 *                                                   row: the new duplicated row
	 *                                                   from: the original row
	 */
	function MultiRowHandler(options)
	{
		this.listeners = {};

		// Overwrite with user's options
		this.options = $.extend(true, {
			$panel: null,
			prefix: 'jform[items]',
			insert: null,
			duplicate: null
		}, options);

		this.$panel = this.options.$panel;
	}

	MultiRowHandler.prototype = {
		/**
		 * Insert a new row
		 *
		 * @param   template  string  The row template to be inserted
		 *
		 * @return  void
		 */
		insert: function(template)
		{
			var $row = this.initializeRow($(template));

			if (typeof this.options.insert === 'function')
			{
				this.options.insert($row);
			}
			else
			{
				this.$panel.append($row);
			}

			this.trigger('afterInsert', $row);
		},

		/**
		 * Duplicate a new row
		 *
		 * @param   $target  jQuery  The row target to be duplicated
		 *
		 * @return  void
		 */
		duplicate: function($target)
		{
			var $row = this.initializeRow($target);

			if (typeof this.options.duplicate === 'function')
			{
				this.options.duplicate($target, $row);
			}
			else
			{
				$target.after($row);
			}

			this.trigger('afterDuplicate', $row, $target);
		},

		/**
		 * Initialize a new row
		 *
		 * @param   $target  jQuery  The row target to be inserted
		 *
		 * @return  jQuery
		 */
		initializeRow: function($target)
		{
			var self = this,
				$row = $target.clone(),
				prefix = self.options.prefix,
				selector = '[name^="' + prefix + '"]',
				newHash = self.generateHash();

			$row.find(selector).each(function()
			{
				var $input = $(this),
					id = $input.attr('id'),
					name = $input.attr('name'),
					inputInfo = self.inputInfo(id, name),
					$targetInput = $target.find('#' + id),
					newName = prefix + '[' + newHash + ']' + '[' + inputInfo.fieldName + ']',
					newId = '';

				newId = newName.replace(/\]\[/g, '_');
				newId = newId.replace(/\[/g, '_');
				newId = newId.replace(/\]/g, '');

				newName += inputInfo.nameSuffix;
				newId += inputInfo.idSuffix;

				$input.val($targetInput.val());

				$input.attr('name', newName);
				$input.attr('id', newId);
			});

			self.trigger('initializeRow', $row);

			return $row;
		},

		/**
		 * Remove a row
		 *
		 * @param   $target  jQuery  The row target to be removed
		 *
		 * @return  void
		 */
		remove: function($target)
		{
			$target.remove();
		},

		/**
		 * Generate hash
		 *
		 * @return  string
		 */
		generateHash: function()
		{
			return (new Date).getTime().toString();
		},

		/**
		 * Get hash and fieldName information
		 *
		 * @param   id    string  Form input id   (expect format: {prefix}_{hash}_{fieldName}{suffix})
		 * @param   name  string  Form input name (expect format: {prefix}[{hash}][{fieldName}]{suffix})
		 *
		 * @return  {{hash:string, fieldName:string, idSuffix:string, nameSuffix:string}}
		 */
		inputInfo: function(id, name)
		{
			var parts = name.replace(this.options.prefix, ''),
				idSuffix = '',
				nameSuffix = '';

			parts = parts.substr(1, parts.length-2).split('][');

			if (parts.length > 2)
			{
				nameSuffix = '[' + parts.slice(2).join('][') + ']';

				var search = name.substr(0, name.length - nameSuffix.length);

				search = search.replace(/\]\[/g, '_');
				search = search.replace(/\[/g, '_');
				search = search.replace(/\]/g, '');

				idSuffix = id.replace(search, '');
			}

			return {
				hash: parts[0] ? parts[0] : '',
				fieldName: parts[1] ? parts[1] : '',
				idSuffix: idSuffix,
				nameSuffix: nameSuffix
			};
		},

		/**
		 * Adds a listener to the end of the listeners array for the specified event.
		 *
		 * @param   event     string
		 * @param   listener  callback
		 *
		 * @return  void
		 */
		on: function(event, listener)
		{
			if (!this.listeners[event])
			{
				this.listeners[event] = [];
			}

			this.listeners[event].push(listener);
		},

		/**
		 * Execute each of the listeners in order with the supplied arguments.
		 *
		 * @param   event  string  Event name
		 * @param   arg    {...*}  Listener arguments
		 *
		 * @return  void
		 */
		trigger: function(event, arg/*, arg2, arg3, ...*/)
		{
			if (!this.listeners[event])
			{
				return;
			}

			var args = Array.prototype.slice.call(arguments, 1),
				callbacks = this.listeners[event],
				max = callbacks.length,
				i;

			for (i = 0; i < max; ++i)
			{
				callbacks[i].apply(this, args);
			}
		}
	};

	// Exports
	window.MultiRowHandler = MultiRowHandler;
})(jQuery);
