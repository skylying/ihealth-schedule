;(function($, undefined)
{
	"use strict";

	if (window.ImageAjaxField !== undefined)
	{
		return;
	}

	/**
	 * Class ImageAjaxField
	 *
	 * @param name           string  Field name
	 * @param imageUriProxy  string  Image uri proxy
	 * @param ajaxPostUri    string  Ajax post uri
	 */
	function ImageAjaxField(name, imageUriProxy, ajaxPostUri)
	{
		/**
		 * Upload file input
		 *
		 * @type {HTMLElement}
		 */
		this.uploadFile = $("#upload-input-" + name);

		/**
		 * Remove image button
		 *
		 * @type {HTMLElement}
		 */
		this.removeImage = $("#remove-input-" + name);

		/**
		 * Display image dataset block
		 *
		 * @type {HTMLElement}
		 */
		this.displayBox = $("#display-box-" + name);

		/**
		 * Upload image input block
		 *
		 * @type {HTMLElement}
		 */
		this.uploadBox = $("#upload-box-" + name);

		/**
		 * Display image
		 *
		 * @type {HTMLElement}
		 */
		this.displayImage = $("#display-image-" + name);

		/**
		 * Display image name
		 *
		 * @type {HTMLElement}
		 */
		this.displayName = $("#display-name-" + name);

		/**
		 * Image database id
		 *
		 * @type {HTMLElement}
		 */
		this.imageId = $("#image-id-" + name);

		/**
		 * Image rx id
		 *
		 * @type {HTMLElement}
		 */
		this.uploadRxId = $("#upload-rx-id-" + name);

		/**
		 * Image uri proxy
		 *
		 * @type {string}
		 */
		this.imageUriProxy = imageUriProxy;

		/**
		 * Ajax post uri
		 *
		 * @type {string}
		 */
		this.ajaxPostUri = ajaxPostUri;


		// 初始化 HTMLElement event

		this.fileInputEvent();

		this.deleteInputEvent();
	}

	ImageAjaxField.prototype = {
		/**
		 * 上傳圖片事件
		 *
		 * @return  void
		 */
		fileInputEvent: function()
		{
			var field = this;

			this.uploadFile.change(function()
			{
				field.uploadImage();
			});
		},

		/**
		 * 刪除圖片事件
		 *
		 * @return  void
		 */
		deleteInputEvent: function()
		{
			var field = this;

			this.removeImage.click(function(event)
			{
				if (confirm("確定要刪除嗎?"))
				{
					field.deleteImage();
				}
			});
		},

		/**
		 * 如果有對應圖片時刷新 field 顯示
		 *
		 * @param   {string}  title 圖片名稱
		 * @param   {string}  path  圖片位置
		 *
		 * @return  void
		 */
		flushImage: function(title, path)
		{
			this.displayBox.show();
			this.uploadBox.hide();

			this.displayImage.attr("src", this.imageUriProxy + "/" + path);
			this.displayName.text(title);
		},

		/**
		 * 上傳圖片
		 *
		 * @return  void
		 */
		uploadImage: function()
		{
			var field = this;
			var post  = new FormData();
			var files = this.uploadFile.prop("files");

			post.append("image", files[0]);
			post.append("rxId", this.uploadRxId.val());
			post.append("task", "image.ajax.upload");

			$.ajax({
				url: this.ajaxPostUri,
				type: "post",
				dataType: "json",
				data: post,
				processData: false,
				contentType: false,
				success: function(data)
				{
					field.imageId.val(data.id);

					field.flushImage(data.title, data.path);
				}
			});
		},

		/**
		 * 刪除圖片
		 *
		 * @return  void
		 */
		deleteImage: function()
		{
			var field = this;

			$.ajax({
				url: this.ajaxPostUri,
				type: "post",
				dataType: "json",
				data: {
					id: this.imageId.val(),
					task: "image.ajax.delete"
				},
				success: function(data)
				{
					// Show file input
					field.displayBox.hide();
					field.uploadBox.show();

					// Clear value
					field.imageId.val("");
				}
			});
		}
	};

	window.ImageAjaxField = ImageAjaxField;
})(jQuery);
