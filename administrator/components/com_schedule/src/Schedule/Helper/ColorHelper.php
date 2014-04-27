<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

/**
 * Class ColorHelper for Namespace.
 *
 * @since 1.0
 */
class ColorHelper
{
	/**
	 * getColorBlock
	 * ex: getColorBlock('#ff0000', 30)            => generate a 30px red square
	 * ex: getColorBlock('red', 20, 'pull-right')  => generate a 20px pull-right red square
	 *
	 * @param string $color
	 * @param int    $size
	 * @param string $class
	 *
	 * @return  string
	 */
	public static function getColorBlock($color = '#eee', $size = 25, $class = null)
	{
		$attributes = array(
			'class' => $class,
			'style' => sprintf('width:%spx; height:%spx; background:%s; margin:0 auto; border-radius:7px;', $size, $size, $color)
		);

		return (string) new \Windwalker\Html\HtmlElement('div', '', $attributes);
	}
}
