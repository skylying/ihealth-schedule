<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper\Form;

/**
 * Class MultiRowHelper
 */
abstract class MultiRowHelper
{
	/**
	 * Get row data attributes
	 *
	 * @param   string  $fieldId
	 * @param   string  $fieldName
	 * @param   string  $fieldFullName
	 *
	 * @return  string
	 */
	public static function getRowDataAttributes($fieldId, $fieldName, $fieldFullName)
	{
		$idPrefix   = preg_replace('/' . $fieldName . '$/', '', $fieldId);
		$namePrefix = preg_replace('/\[' . $fieldName . '\]$/', '', $fieldFullName);

		// Generate replace string for field id
		$parts = explode('_', trim($idPrefix, '_ '));
		array_pop($parts);
		$idReplace = implode('_', $parts) . '_{{rowHash}}_';

		// Generate replace string for field name
		$parts = explode('][', $namePrefix);
		array_pop($parts);
		$nameReplace = implode('][', $parts) . '][{{rowHash}}]';

		$html[] = 'data-id-prefix="' . $idPrefix . '"';
		$html[] = 'data-name-prefix="' . $namePrefix . '"';
		$html[] = 'data-id-replace="' . $idReplace . '"';
		$html[] = 'data-name-replace="' . $nameReplace . '"';

		return implode(' ', $html);
	}
}
