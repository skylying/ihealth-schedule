(function($, undefined)
{
	"use strict";

	if (window.Address !== undefined)
	{
		return;
	}

	/**
	 * Address Object (For Chosen version)
	 *
	 * @see http://harvesthq.github.io/chosen/
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

				$area.chosen('destroy');
				$area.chosen();
			}

			// Initialize area selection when city value is changed
			$city.chosen().change(function(e)
			{
				console.log(e.target.value);

				var cityId = e.target.value,
					areaOptions = self.areas[cityId] || '';

				$area.html(areaOptions);
				$area.val('');

				$area.chosen('destroy');
				$area.chosen();
			});
		}
	};
})(jQuery);
