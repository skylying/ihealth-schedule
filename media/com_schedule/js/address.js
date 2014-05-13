(function($, undefined)
{
	"use strict";

	if (window.Address !== undefined)
	{
		return;
	}

	/**
	 * Address Object
	 */
	window.Address =
	{
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

			// Initialize area selection element
			if (areaValue)
			{
				$area.html(areaOptions);
				$area.val(areaValue);
			}

			// Initialize area selection when city value is changed
			$city.change(function()
			{
				var cityId = $(this).val(),
					areaOptions = self.areas[cityId] || '';

				$area.html(areaOptions);
				$area.val('');
			});
		}
	};
})(jQuery);
