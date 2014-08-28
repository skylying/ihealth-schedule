/**
 * class ImageUploader
 */
;(function(global, undefined)
{
	"use strict";

	var $ = global.jQuery;

	if (global.ImageUploader !== undefined)
	{
		return;
	}

	/**
	 * Class ImageUploader
	 *
	 * CSS class list:
	 * - thumb: The thumb container
	 * - thumb-loading: The thumb loading text container
	 * - browse-button: Browse file explorer button
	 * - delete-button: Delete file button
	 *
	 * @param {string}   containerId             Uploader container id
	 * @param {object}   options                 Configuration parameters
	 * @param {string}   options.baseUrl         (Required) API base URL
	 * @param {string}   options.type            (Required) Image type, includes "rxindividual": 散客上傳, "hospital": 醫院示範圖例
	 * @param {string}   options.suffix          (Optional) Image file suffix
	 * @param {string}   options.uploadTask      (Optional) Upload API URL
	 * @param {string}   options.deleteTask      (Optional) Delete API URL
	 * @param {callback} options.afterUpload     (Optional) A callback function to be triggered after an image is uploaded
	 * @param {callback} options.afterRemove     (Optional) A callback function to be triggered after an image is removed
	 * @param {callback} options.uploadExtraData (Optional) A callback function to send extra image data when uploading an image
	 * @param {integer}  options.thumbMaxWidth   (Optional) Thumb image file max width, pixel unit
	 * @param {integer}  options.thumbMaxHeight  (Optional) Thumb image file max height, pixel unit
	 */
	function ImageUploader(containerId, options)
	{
		this.containerId = containerId;

		this.options = $.extend(true, {
			baseUrl: '',
			type: '',
			suffix: '',
			uploadTask: 'image.ajax.upload',
			deleteTask: 'image.ajax.delete',
			afterUpload: function(image) {},
			afterRemove: function() {},
			uploadExtraData: function() { return {}; },
			thumbMaxWidth: 360,
			thumbMaxHeight: 360
		}, options);
	}

	global.ImageUploader = ImageUploader;

	var prototype = ImageUploader.prototype;

	prototype.init = function()
	{
		// Initialize jQuery selectors
		this.$container = $('#' + this.containerId);
		this.$file = this.$container.find('input[type="file"]');
		this.$input = this.$container.find('input[type="hidden"]');
		this.$btnBrowse = this.$container.find('.browse-button:first');
		this.$btnDelete = this.$container.find('.delete-button:first');
		this.$thumb = this.$container.find('.thumb:first');
		this.$thumbLoading = this.$container.find('.thumb-loading:first');

		this.registerEvents();

		this.$thumbLoading.hide();
		this.$btnBrowse.hide();
		this.$btnDelete.hide();

		if (this.$input.val() > 0)
		{
			this.$btnDelete.show();
		}
		else
		{
			this.$btnBrowse.show();
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

		self.$btnDelete.on('click', function()
		{
			self.remove();
		});

		self.$file.on('change', function()
		{
			self.upload();
		});
	};

	/**
	 * Upload an image
	 *
	 * Uploaded image data format as below:
	 * - id: Image id
	 * - title: Image title
	 * - url: Image URL path
	 *
	 * @returns void
	 */
	prototype.upload = function()
	{
		var self = this,
			data = new FormData(),
			files = self.$file.prop('files'),
			extra = self.uploadExtraData();

		self.$thumbLoading.show();
		self.$btnBrowse.prop('disabled', true);

		data.append('image', files[0]);
		data.append('type', self.options.type);
		data.append('suffix', self.options.suffix);
		data.append('option', 'com_schedule');
		data.append('task', self.options.uploadTask);

		for (var key in extra)
		{
			if (extra.hasOwnProperty(key))
			{
				data.append('jform[' + key + ']', extra[key]);
			}
		}

		$.ajax({
			url: self.options.baseUrl,
			type: 'post',
			dataType: 'json',
			data: data,
			processData: false,
			contentType: false,
			success: function(image)
			{
				self.$input.val(image.id);

				self.displayThumb(image);

				self.$btnBrowse.prop('disabled', false);
				self.$btnBrowse.hide();
				self.$btnDelete.show();

				self.afterUpload(image);
			}
		});
	};

	/**
	 * Remove an image
	 *
	 * @returns void
	 */
	prototype.remove = function()
	{
		var self = this,
			id = self.$input.val();

		if ('0' === id)
		{
			return;
		}

		self.$btnDelete.prop('disabled', true);

		var data = {
			task: this.options.deleteTask,
			id: id
		};

		$.post(
			this.baseUrl,
			data,
			function()
			{
				self.$input.val('0');
				self.$thumb.html('');

				self.$btnDelete.prop('disabled', false);
				self.$btnDelete.hide();
				self.$btnBrowse.show();

				self.afterRemove();
			},
			'json'
		);
	};

	/**
	 * Display thumb image
	 *
	 * @param {object} image Uploaded image data
	 *
	 * @returns void
	 *
	 * @see upload
	 */
	prototype.displayThumb = function(image)
	{
		var self = this,
			$html = $('<a href="" target="_blank"><img src="" alt="" /></a>'),
			$img = $html.find('img:first');

		$html.prop('href', image.url);

		$img.hide();
		$img.prop('alt', image.title);
		$img.prop('src', image.url);

		self.$thumb.html($html);

		$img.on('load', function()
		{
			var width = self.options.thumbMaxWidth,
				height = self.options.thumbMaxHeight,
				ratioWidth = width / this.width,
				ratioHeight = height / this.height;

			// Change height or width value
			if (ratioHeight > ratioWidth)
			{
				height = ratioWidth * this.height;
			}
			else
			{
				width = ratioHeight * this.width;
			}

			this.width = width;
			this.height = height;

			self.$thumbLoading.hide();

			$img.show();
		});
	};

	/**
	 * Trigger after uploading an image
	 *
	 * @param {object} image Image data
	 *
	 * @returns void
	 */
	prototype.afterUpload = function(image)
	{
		if (typeof this.options.afterUpload === 'function')
		{
			this.options.afterUpload(image);
		}
	};

	/**
	 * Trigger after removing an image
	 *
	 * @returns void
	 */
	prototype.afterRemove = function()
	{
		if (typeof this.options.afterRemove === 'function')
		{
			this.options.afterRemove();
		}
	};

	/**
	 * Get extra data before uploading an image
	 *
	 * @returns {object}
	 */
	prototype.uploadExtraData = function()
	{
		if (typeof this.options.uploadExtraData === 'function')
		{
			return this.options.uploadExtraData();
		}

		return {};
	};

})(window);
