/**
 * class ImageFile
 */
;(function(global, undefined)
{
	"use strict";

	var $ = global.jQuery;

	if (global.ImageFile !== undefined)
	{
		return;
	}

	/**
	 * Class ImageFile
	 *
	 * CSS class list:
	 * - thumb: The thumb container
	 * - thumb-loading: The thumb loading text container
	 * - browse-button: Browse file explorer button
	 *
	 * @param {string}   containerId            Uploader container id
	 * @param {object}   options                Configuration parameters
	 * @param {string}   options.imageUrl       (Optional) Image URL
	 * @param {integer}  options.thumbMaxWidth  (Optional) Thumb image file max width, pixel unit
	 * @param {integer}  options.thumbMaxHeight (Optional) Thumb image file max height, pixel unit
	 * @param {string}   options.imagePath      (Optional) Image path
	 */
	function ImageFile(containerId, options)
	{
		this.containerId = containerId;

		this.options = $.extend(true, {
			imageUrl: '',
			thumbMaxWidth: 360,
			thumbMaxHeight: 360,
			imagePath: ''
		}, options);
	}

	global.ImageFile = ImageFile;

	var prototype = ImageFile.prototype;

	prototype.init = function()
	{
		// Initialize jQuery selectors
		this.$container = $('#' + this.containerId);
		this.$file = this.$container.find('input[type="file"]');
		this.$btnBrowse = this.$container.find('.browse-button:first');
		this.$thumb = this.$container.find('.thumb:first');
		this.$thumbLoading = this.$container.find('.thumb-loading:first');

		this.registerEvents();

		if (this.options.imageUrl)
		{
			this.displayRemoteThumb();
		}
	};

	/**
	 * Register element events
	 *
	 * @returns void
	 */
	prototype.registerEvents = function()
	{
		var self = this;

		self.$btnBrowse.on('click', function()
		{
			self.$file.click();
		});

		self.$file.on('change', function()
		{
			self.$thumbLoading.show();
			self.$btnBrowse.prop('disabled', true);

			self.displayLocalThumb(this.files[0]);
		});
	};

	/**
	 * Display remote image thumbnail
	 */
	prototype.displayRemoteThumb = function()
	{
		var self = this,
			$html = $('<a href="" target="_blank"><img src="" alt="" /></a>'),
			$img = $html.find('img:first');

		$html.prop('href', self.options.imageUrl);

		$img.hide();
		$img.prop('alt', this.options.imagePath);
		$img.prop('src', this.options.imageUrl);

		self.$thumb.html($html);

		$img.on('load', function()
		{
			resizeImage(this, self.options.thumbMaxWidth, self.options.thumbMaxHeight);

			self.$thumbLoading.hide();

			$img.show();
		});
	};

	/**
	 * Display local image thumbnail
	 *
	 * @param {File} file Local image file
	 *
	 * @returns void
	 */
	prototype.displayLocalThumb = function(file)
	{
		var self = this,
			$img = $('<img src="" alt="" />'),
			reader = new FileReader();

		$img.hide();
		$img.prop('file', file);
		$img.prop('alt', this.options.imageUrl);

		reader.onload = function(e)
		{
			$img.prop('src', e.target.result);
		};

		reader.readAsDataURL(file);

		self.$thumb.html($img);

		$img.on('load', function()
		{
			resizeImage(this, self.options.thumbMaxWidth, self.options.thumbMaxHeight);

			self.$thumbLoading.hide();

			$img.show();
		});

		self.$btnBrowse.prop('disabled', false);
	};

	/**
	 * Resize image
	 *
	 * @param {object} img       An img element
	 * @param {int}    maxWidth  Max image width
	 * @param {int}    maxHeight Max image height
	 *
	 * @returns void
	 */
	function resizeImage(img, maxWidth, maxHeight)
	{
		var width = maxWidth,
			height = maxHeight,
			ratioWidth = img.width / width,
			ratioHeight = img.height / height;

		// Change height or width value
		if (ratioWidth > ratioHeight)
		{
			height = img.height * height / img.width;
		}
		else
		{
			width = img.width * width / img.height;
		}

		img.width = width;
		img.height = height;
	}

})(window);
