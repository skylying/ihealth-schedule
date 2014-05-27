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
class JFormFieldImage extends \JFormField
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
		$name      = $this->element['name'];
		$textOnly  = XmlHelper::getBool($this->element, 'text_only', false);
		$rxIdField = XmlHelper::getAttribute($this->element, 'rx_id_field', "id");
		$inputName = $this->name;
		$image     = ! empty($this->value) ? (new DataMapper(Table::IMAGES))->findOne($this->value) : new Data;
		$imageId   = "";
		$baseUrl   = JUri::root(true);
		$ajaxUrl   = JURI::getInstance();
		$imagePath = "";
		$imageName = "";
		$imageBox  = "display: none;";
		$inputBox  = "";
		$imgClass  = "col-lg-4";
		$nameClass = "col-lg-6";

		// 沒有圖片時的設定值
		if (! $image->isNull())
		{
			$imageBox  = "";
			$inputBox  = "display: none;";
			$imageId   = $image->id;
			$imageName = $image->title;
			$imagePath = $baseUrl . "/" . $image->path;
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
		$rxId = $app->input->get($rxIdField, 0);

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
		<div class="col-lg-2">
			<button id="remove-input-{$name}" type="button" class="btn">刪除</button>
		</div>
	</div>

	<!-- Upload -->
	<div id="upload-box-{$name}" class="row" style="{$inputBox}">
		<input id="upload-input-{$name}" name="image" type="file" />
		<input id="upload-rx-id-{$name}" name="rxId"  type="hidden" value="{$rxId}" />
	</div>
	<input id="image-id-{$name}" type="hidden" name="{$inputName}" value="{$imageId}" />
HTML;

		return $html;
	}
}
