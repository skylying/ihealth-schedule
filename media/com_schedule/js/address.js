/**
 * Address Helper
 */
;(function($, undefined)
{
	"use strict";

	if (window.Address !== undefined)
	{
		return;
	}

	/**
	 * Address Object
	 */
	window.Address = {
		/**
		 * Area HTML selection options
		 *
		 * @type  {object}
		 */
		areas: {},

		/**
		 * Set area HTML selection options
		 *
		 * @param  {object}  areas
		 */
		setAreas: function(areas)
		{
			this.areas = areas;
		},

		/**
		 * Bind field events
		 *
		 * @param  {string}  cityId      City selection element id
		 * @param  {string}  areaId      Area selection element id
		 * @param  {int}     areaValue=  (Optional) Selected area id value
		 */
		bind: function(cityId, areaId/*, areaValue*/)
		{
			var self = this,
				$city = $('#' + cityId),
				$area = $('#' + areaId),
				areaOptions = self.areas[$city.val()] || '',
				areaValue = Array.prototype.slice.call(arguments, 2, 3)[0] || null;

			// Initialize area selection when city value is changed
			$city.change(function()
			{
				updateAreaSelection($(this).val());
			});

			updateAreaSelection($city.val());

			// Initialize area selection element
			if (areaValue)
			{
				$area.html(areaOptions);
				$area.val(areaValue);

				// Trigger Chosen update event
				if ($area.data('chosen'))
				{
					$area.trigger('liszt:updated');
				}
			}

			/**
			 * Update area selection
			 *
			 * @param {int} city
			 *
			 * @private
			 */
			function updateAreaSelection(city)
			{
				var areaOptions = self.areas[city] || '';

				$area.html(areaOptions);
				$area.val('');
				$area.attr("data-placeholder", "--選擇區域--");

				// Trigger Chosen update event
				if ($area.data('chosen'))
				{
					$area.trigger('liszt:updated');
				}
			}
		}
	};
})(jQuery);
