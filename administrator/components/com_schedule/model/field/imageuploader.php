<?php

use Windwalker\Data\Data;
use Windwalker\Helper\XmlHelper;
use Windwalker\View\Layout\FileLayout;
use Windwalker\DI\Container;
use Schedule\Table\Table;
use Windwalker\Joomla\DataMapper\DataMapper;

JFormHelper::loadFieldClass('file');

/**
 * Class JFormFieldImageUploader
 *
 * The value in this field type is "image id", only accept an integer value
 *
 * XML properties:
 * - image_type:        (Required) Image type, includes "rxindividual": 散客上傳, "hospital": 醫院示範圖例
 * - image_file_suffix: (Optional) Image file suffix
 * - upload_task:       (Optional) Upload task name (Default is "image.ajax.upload")
 * - delete_task:       (Optional) Delete task name (Default is "image.ajax.delete")
 * - thumb_max_width:   (Optional) Thumb image file max width, pixel unit (Default is 360)
 * - thumb_max_height:  (Optional) Thumb image file max height, pixel unit (Default is 360)
 * - layout_name:       (Optional) A layout name to display input field (Default is "image.uploader.default")
 * - after_upload:      (Optional) A javascript callback function to be triggered after uploading an image file
 *                                 Callback function format: function (image) {}
 * - after_remove:      (Optional) A javascript callback function to be triggered after removing an image file
 *                                 Callback function format: function () {}
 * - upload_extra_data: (Optional) A javascript callback function to be triggered before uploading an image file
 *                                 In order to get extra uploading data form this callback, the callback should
 *                                 return an object with extra data.
 *                                 Callback function format: function () { return {foo:"bar"}; }
 *
 * @since 1.0
 */
class JFormFieldImageUploader extends JFormFieldFile
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'ImageUploader';

	/**
	 * Check if this field type is initialized or not.
	 *
	 * @var bool
	 */
	protected static $initialized = false;

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->image_type        = (string) XmlHelper::get($element, 'image_type');
			$this->image_file_suffix = (string) XmlHelper::get($element, 'image_file_suffix');
			$this->upload_task       = (string) XmlHelper::get($element, 'upload_task', 'image.ajax.upload');
			$this->delete_task       = (string) XmlHelper::get($element, 'remove_task', 'image.ajax.delete');
			$this->thumb_max_width   = (int) XmlHelper::get($element, 'thumb_max_width', 360);
			$this->thumb_max_height  = (int) XmlHelper::get($element, 'thumb_max_height', 360);
			$this->layout_name       = (string) XmlHelper::get($element, 'layout_name', 'image.uploader.default');
			$this->after_upload      = (string) XmlHelper::get($element, 'after_upload');
			$this->after_remove      = (string) XmlHelper::get($element, 'after_remove');
			$this->upload_extra_data = (string) XmlHelper::get($element, 'upload_extra_data');

			// The value is image id, only accept an integer value
			$this->value = (int) $value;

			// Accept image file type only
			if (empty($this->accept) || !preg_match('/image/i', $this->accept))
			{
				$this->accept = 'image/*';
			}
		}

		return $return;
	}

	/**
	 * getInput
	 *
	 * @return  string
	 */
	public function getInput()
	{
		$this->init();

		$fileInput = '<input type="file" value="" accept="' . $this->accept . '" />';
		$input = '<input type="hidden" name="' . $this->name . '" ' .
			'id="' . $this->id . '" value="' . $this->value . '" />';

		$displayData = array(
			'containerId' => $this->id . '-uploader',
			'fileInput' => $fileInput,
			'input' => $input,
			'image' => null,
			'field' => $this,
			'jsOptions' => array(
				'baseUrl'        => (string) JUri::getInstance(),
				'type'           => $this->image_type,
				'suffix'		 => $this->image_file_suffix,
				'uploadTask'     => $this->upload_task,
				'deleteTask'     => $this->delete_task,
				'thumbMaxWidth'  => $this->thumb_max_width,
				'thumbMaxHeight' => $this->thumb_max_height,
			),
		);

		if ($this->value > 0)
		{
			$imageMapper = new DataMapper(Table::IMAGES);
			$image = $imageMapper->findOne($this->value);

			if (!$image->isNull())
			{
				$image['url'] = $image['path'];

				if (!preg_match('#^(http|https|ftp)://#i', $image['path']))
				{
					$image['url'] = JUri::root() . $image['path'];
				}

				$displayData['image'] = $image;
			}
		}

		$layout = new FileLayout($this->layout_name);

		return $layout->render(new Data($displayData));
	}

	/**
	 * init
	 *
	 * @return  void
	 */
	public function init()
	{
		if (true === self::$initialized)
		{
			return;
		}

		/** @var \Windwalker\Helper\AssetHelper $asset */
		$asset = Container::getInstance('com_schedule')->get('helper.asset');

		$asset->addJs('js/image-uploader.js');

		self::$initialized = true;
	}
}
