<?php

use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Windwalker\Helper\XmlHelper;

use Schedule\Table\Table;

/**
 * Class JFormFieldImage
 *
 * XML properties:
 * - text_only :   (optional) 只顯示文字模式
 *                 1. True:  field 只顯示圖片標題
 *                 2. False: field 會顯示圖片與標題
 *
 * - rx_id_field : (optional) 圖片處方編號會依照這參數的設定去網址取得對應內容
 *                 Default 值為 "id"
 *
 * @since 1.0
 */
class JFormFieldImage extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Image';

	/**
	 * getInput
	 *
	 * @return  string
	 */
	public function getInput()
	{
		// Prepare image information
		$imageInfo = $this->getImageInfo();

		// Get xml attributes
		$textOnly       = XmlHelper::getBool($this->element, 'text_only', false);
		$foreignIdField = XmlHelper::getAttribute($this->element, 'foreign_id_field', "id");
		$imageType      = XmlHelper::get($this->element, 'imageType');
		$purpose        = XmlHelper::get($this->element, 'purpose', '');

		// Miscelleneous configs
		$name      = $this->element['name'];
		$inputName = $this->name;
		$imageId   = "";
		$baseUrl   = JUri::root(true);
		$ajaxUrl   = JURI::getInstance();
		$imagePath = "";
		$imageName = "";
		$imageBox  = "display: none;";
		$inputBox  = "";
		$imgClass  = "col-lg-6 col-md-6 col-sm-6";
		$nameClass = "col-lg-4 col-md-4 col-sm-4";

		// 沒有圖片時的設定值
		if (! $imageInfo->isNull())
		{
			$imageBox  = "";
			$inputBox  = "display: none;";
			$imageId   = $imageInfo->id;
			$imageName = $imageInfo->title;
			$imagePath = $baseUrl . "/" . $imageInfo->path;
		}

		// 純文字時的設定值
		if ($textOnly)
		{
			$imgClass  = "hide";
			$nameClass = "col-lg-10";
		}

		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		// TODO: 之後修改成從 form 找尋 rx id
		$foreignId = $app->input->get($foreignIdField, 0);

		$doc->addScript(JUri::root(true) . "/media/com_schedule/js/image/ajax-field.js");

		$doc->addScriptDeclaration(
			<<<JS
	jQuery(
		function()
		{
			if (undefined == window.imageAjax)
			{
				window.imageAjax = [];
			}

			window.imageAjax['{$name}'] = new ImageAjaxField('{$name}', '{$baseUrl}', '{$ajaxUrl}');
		}
	);
JS
);

		$html = <<<HTML
	<!-- Display image -->
	<div id="display-box-{$name}" class="row" style="{$imageBox}">
		<!-- image -->
		<img id="display-image-{$name}" class="{$imgClass}" src="{$imagePath}" />

		<!-- image name -->
		<div id="display-name-{$name}" class="{$nameClass}">
			{$imageName}
		</div>

		<!-- remove button -->
		<div class="col-lg-2 col-md-2 col-sm-2">
			<button id="remove-input-{$name}" type="button" class="btn btn-danger">刪除</button>
		</div>
	</div>

	<!-- Upload -->
	<div id="upload-box-{$name}" class="row" style="{$inputBox}">
		<input id="upload-input-{$name}" name="image" type="file" />
		<input id="upload-foreign-id-{$name}" name="$foreignId"  type="hidden" value="{$foreignId}" />
		<input id="upload-type-{$name}" name="imageType"  type="hidden" value="{$imageType}" />
		<input id="image-purpose-{$name}" name="purpose"  type="hidden" value="{$purpose}" />
	</div>
	<input id="image-id-{$name}" type="hidden" name="{$inputName}" value="{$imageId}" />
HTML;

		return $html;
	}

	/**
	 * getImageInfo
	 *
	 * @return  mixed|Data
	 */
	public function getImageInfo()
	{
		$imageDataMapper = new DataMapper(Table::IMAGES);

		$result = empty($this->value) ? new Data : $imageDataMapper->findOne($this->value);

		return $result;
	}
}
