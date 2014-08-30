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
 * The value in this field type is "image path"
 *
 * XML properties:
 * - thumb_max_width:  (Optional) Thumb image file max width, pixel unit (Default is 360)
 * - thumb_max_height: (Optional) Thumb image file max height, pixel unit (Default is 360)
 * - layout_name:      (Optional) A layout name to display input field (Default is "image.file.default")
 *
 * @since 1.0
 */
class JFormFieldImageFile extends JFormFieldFile
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'ImageFile';

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
			$this->thumb_max_width  = (int) XmlHelper::get($element, 'thumb_max_width', 360);
			$this->thumb_max_height = (int) XmlHelper::get($element, 'thumb_max_height', 360);
			$this->layout_name      = (string) XmlHelper::get($element, 'layout_name', 'image.file.default');

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

		$input = parent::getInput();
		$imageUrl = '';

		if ($this->value > 0)
		{
			$imageMapper = new DataMapper(Table::IMAGES);
			$image = $imageMapper->findOne($this->value);

			if (!$image->isNull())
			{
				$imageUrl = $image['path'];

				if (!preg_match('#^(http|https|ftp)://#i', $imageUrl))
				{
					$imageUrl = JUri::root() . $image['path'];
				}
			}
		}

		$displayData = array(
			'containerId' => $this->id . '-container',
			'input' => $input,
			'jsOptions' => array(
				'imageUrl'       => $imageUrl,
				'thumbMaxWidth'  => $this->thumb_max_width,
				'thumbMaxHeight' => $this->thumb_max_height,
				'imagePath'      => $this->value,
			),
		);

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

		$asset->addJs('js/image-file.js');

		self::$initialized = true;
	}
}
