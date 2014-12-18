<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Class JFormRuleDate
 *
 * @since 1.0
 */
class JFormRuleDate extends JFormRule
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var    string
	 * @since  11.1
	 * @see    http://www.w3.org/TR/html-markup/input.email.html
	 */
	protected $regex = '20[0-9]{2}-[0-1][0-9]-[0-3][0-9]$';
}
