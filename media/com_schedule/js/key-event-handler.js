/**
 * KeyEventHandler Javascript Library v1.0
 */
;(function($, undefined)
{
	"use strict";

	/* Globals: document, window, console */

	if (window.KeyEventHandler !== undefined)
	{
		return;
	}

	/**
	 * Class KeyEventHandler
	 *
	 * - Used key list
	 *  - 37 left arrow
	 *  - 39 right arrow
	 *  - 13 enter
	 *
	 * @param options
	 * @constructor
	 */
	function KeyEventHandler(options)
	{
		// Overwrite with user's options
		this.options = $.extend(true, {
			$panel: null
		}, options);

		this.$panel = this.options.$panel;

		// Double keydown variables
		this.doubleKeyFlag = false;
		this.lastKeyPressTime = 0;
		this.doubleClickInterval = 150;

		this.bindKeys();
	}

	KeyEventHandler.prototype = {

		bindKeys : function()
		{
			var self = this;

			var LEFT = 37,
				RIGHT = 39,
				ENTER = 13;

			/*
			 * Bind <select> key event
			 */
			self.$panel.on('keydown', 'select, textarea', function(e)
			{
				// Bind right arrow key
				if(e.keyCode == RIGHT || e.which == RIGHT)
				{
					// For windows system, prevent RIGHT arrow key triggering <select> onchange
					e.preventDefault();

					self.focusNext(this);
				}
				// Bind left arrow key
				else if (e.keyCode == LEFT || e.which == LEFT)
				{
					// For windows system, prevent LEFT arrow key triggering <select> onchange
					e.preventDefault();

					self.focusPrev(this);
				}
			});

			/*
			 * Bind <input type="checkbox"> key event
			 */
			self.$panel.on('keydown', 'input[type="checkbox"]:not([disabled])', function(e)
			{
				// Bind right arrow key
				if(e.keyCode == RIGHT || e.which == RIGHT)
				{
					self.focusNextCb(this);
				}
				// Bind left arrow key
				else if (e.keyCode == LEFT || e.which == LEFT)
				{
					self.focusPrevCb(this);
				}
				// Bind enter key event
				else if (e.keyCode == ENTER || e.which == ENTER)
				{
					$(this).click();
				}
			});

			/*
			 * Bind <button> key event
			 */
			self.$panel.on('keydown', 'button', function(e)
			{
				// Bind right arrow key
				if(e.keyCode == RIGHT || e.which == RIGHT)
				{
					self.focusNextBtn(this);
				}
				// Bind left arrow key
				else if (e.keyCode == LEFT || e.which == LEFT)
				{
					self.focusPrevBtn(this);
				}
			});

			/*
			 * Bind <input type="text"> (就醫日期) double keydown event
			 *
			 * 快速點兩下右鍵可以 focus 到下一個 <td>
			 * 快速點兩下左鍵可以 focus 到上一個 <select2>
			 *
			 */
			self.$panel.on('keydown', 'input[type="text"]', function(e)
			{
				// Bind right arrow key
				if (e.keyCode == RIGHT || e.which == RIGHT)
				{
					if (self.doubleKeyFlag)
					{
						var thisKeyPressTime = new Date().getTime();

						if (thisKeyPressTime - self.lastKeyPressTime <= self.doubleClickInterval)
						{
							self.focusNext(this);
						}

						self.doubleKeyFlag = false;
						self.lastKeyPressTime = thisKeyPressTime;
					}

					self.lastKeyPressTime = new Date().getTime();
					self.doubleKeyFlag = true;
				}
				// Bind left arrow key
				else if (e.keyCode == LEFT || e.which == LEFT)
				{
					if (self.doubleKeyFlag)
					{
						var thisKeyPressTime = new Date().getTime();

						if (thisKeyPressTime - self.lastKeyPressTime <= self.doubleClickInterval)
						{
							self.focusPrev(this);
						}

						self.doubleKeyFlag = false;
						self.lastKeyPressTime = thisKeyPressTime;
					}

					self.lastKeyPressTime = new Date().getTime();
					self.doubleKeyFlag = true;
				}
			});

			/*
			 * Bind add 10 row <button> focusout event
			 */
			$('.add-10-row').on('focusout', function()
			{
				var select2Node = self.$panel.find('.customer-id-selection');

				if (select2Node.length > 0)
				{
					select2Node.select2('open');
				}
			});

			/*
			 * Bind all add-row <button> key event
			 */
			$('.button-add-row').on('keydown', function(e)
			{
				// Bind right arrow key
				if(e.keyCode == RIGHT || e.which == RIGHT)
				{
					var $nextNode = $(this).next();

					if ($nextNode.length < 1)
					{
						$(this).trigger('focusout');
					}

					$nextNode.focus();
				}
				// Bind left arrow key
				else if (e.keyCode == LEFT || e.which == LEFT)
				{
					var $prevNode = $(this).prev();

					if ($prevNode.length < 1)
					{
						return true;
					}

					$prevNode.focus();
				}
			});

			$('#s2id_jform_institute_id_selection input[type="text"]').on('keydown', function(e)
			{
				// Bind right arrow key
				if(e.keyCode == RIGHT || e.which == RIGHT)
				{
					if (self.doubleKeyFlag)
					{
						var thisKeyPressTime = new Date().getTime();

						if (thisKeyPressTime - self.lastKeyPressTime <= self.doubleClickInterval)
						{
							$('.add-1-row').focus();
						}

						self.doubleKeyFlag = false;
						self.lastKeyPressTime = thisKeyPressTime;
					}

					self.lastKeyPressTime = new Date().getTime();
					self.doubleKeyFlag = true;
				}
			})
		},

		/**
		 * Focus next <td>
		 *
		 * @param {object} element
		 */
		focusNext : function(element)
		{
			var $nextField = $(element).closest('td').next();

			// If next node is "藥品吃完日" <td>, we jump again
			if ($nextField.hasClass('drug-finish-date'))
			{
				$nextField = $nextField.next();
			}

			$nextField.find('select, input:not([disabled]):first, textarea, button:first').focus();
		},

		/**
		 * Focus previous <td>
		 *
		 * @param {object} element
		 */
		focusPrev : function(element)
		{
			var $prevField = $(element).closest('td').prev();

			// If next node is "藥品吃完日" <td>, we jump again
			if ($prevField.hasClass('drug-finish-date'))
			{
				$prevField = $prevField.prev();
			}
			else if ($prevField.find('.customer-id-selection').length > 0)
			{
				$prevField.find('.customer-id-selection').select2('open');

				return true;
			}

			$prevField.find('select, input:not([disabled]):last, textarea').focus();
		},

		/**
		 * Focus next <input type="checkbox">
		 *
		 * @param {object} element
		 */
		focusNextCb : function(element)
		{
			var self = this,
				$nextNode = $(element).parent().next();

			// If next node is <li>, keep searching
			if ($nextNode.prop('tagName') == 'LI')
			{
				var availableCb = $nextNode.find('input:not([disabled])');

				if (availableCb.length < 1)
				{
					self.focusNext(element);
				}
				else
				{
					availableCb.focus();
				}
			}
			// If next node is not <li>, focus next <td>
			else
			{
				self.focusNext(element);
			}
		},

		/**
		 * Focus previous <input type="checkbox">
		 *
		 * @param {object} element
		 */
		focusPrevCb : function(element)
		{
			var self = this,
				$prevNode = $(element).parent().prev();

			// If next node is <li>, keep searching
			if ($prevNode.prop('tagName') == 'LI')
			{
				var availableCb = $prevNode.find('input:not([disabled])');

				if (availableCb.length < 1)
				{
					self.focusPrev(element);
				}
				else
				{
					availableCb.focus();
				}
			}
			// If previous node is not <li>, focus previous <td>
			else
			{
				self.focusPrev(element);
			}
		},

		/**
		 * Focus next <buutton>
		 *
		 * @param {object} element
		 */
		focusNextBtn : function(element)
		{
			var self = this,
				$nextNode = $(element).next();

			// Time to go to next row
			if ($nextNode.length < 1)
			{
				$(element).closest('tr').next().find('.customer-id-selection').select2('open');
			}
			else
			{
				$nextNode.focus();
			}
		},

		/**
		 * Focus prevous <button>
		 *
		 * @param {object} element
		 */
		focusPrevBtn : function(element)
		{
			var self = this,
				$prevNode = $(element).prev();

			if ($prevNode.length < 1)
			{
				self.focusPrev(element);
			}
			else
			{
				$prevNode.focus();
			}
		}
	};

	// Exports
	window.KeyEventHandler = KeyEventHandler;
})(jQuery);
