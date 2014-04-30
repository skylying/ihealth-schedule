<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper\Form;

/**
 * Class UiHelper
 *
 * @since 1.0
 */
abstract class FieldHelper
{
	/**
	 * resetGroup
	 *
	 * @param  \JFormField  $field
	 * @param  string       $group
	 *
	 * @return  \JFormField
	 */
	public static function resetGroup($field, $group)
	{
		// Modify field's group name
		$field->group = $group;

		// Update field's form name
		$field->name = $field->fieldname;

		// Update field's form id
		$field->id = $field->getAttribute('id');

		return $field;
	}
}
