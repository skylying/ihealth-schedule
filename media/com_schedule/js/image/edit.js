/**
 * class ImageEdit
 */
;(function(global, undefined)
{
	"use strict";

	if (global.ImageEdit !== undefined)
	{
		return;
	}

	var $ = global.jQuery,
		$controlRxId,
		$controlHospitalId,
		$rxImage,
		$controlRxImage,
		$hospitalRxSample,
		$controlHospitalRxSample;

	/**
	 * Class ImageEdit
	 */
	global.ImageEdit = {
		/**
		 * Run image.edit page scripts
		 */
		run: function()
		{
			this.init();
			this.registerEvents();

			showUploadField($('#jform_type input:checked').val());
		},

		/**
		 * Initialization
		 */
		init: function()
		{
			// Initialize jQuery selector
			$controlRxId = $('#control_jform_rx_id');
			$controlHospitalId = $('#control_jform_hospital_id');
			$rxImage = $('#jform_rx_image');
			$controlRxImage = $('#control_jform_rx_image');
			$hospitalRxSample = $('#jform_hospital_rx_sample');
			$controlHospitalRxSample = $('#control_jform_hospital_rx_sample');

			// Hide fields after page loaded
			$controlRxId.hide();
			$controlHospitalId.hide();
			$controlRxImage.hide();
			$controlHospitalRxSample.hide();
		},

		/**
		 * Register element events
		 */
		registerEvents: function()
		{
			// Display proper upload field
			$('#jform_type input').on('click', function()
			{
				showUploadField($(this).val());
			});
		}
	};

	/**
	 * showUploadField
	 *
	 * @param {string} type Image type
	 */
	function showUploadField(type)
	{
		switch (type)
		{
			case 'rxindividual':
				$controlRxId.show();
				$controlHospitalId.hide();
				$rxImage.prop('disabled', false);
				$controlRxImage.show();
				$hospitalRxSample.prop('disabled', true);
				$controlHospitalRxSample.hide();
				break;

			case 'hospital':
				$controlRxId.hide();
				$controlHospitalId.show();
				$rxImage.prop('disabled', true);
				$controlRxImage.hide();
				$hospitalRxSample.prop('disabled', false);
				$controlHospitalRxSample.show();
				break;
		}
	}

})(window);
