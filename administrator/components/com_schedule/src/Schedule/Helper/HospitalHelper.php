<?php

namespace Schedule\Helper;

/**
 * Class HospitalHelper
 *
 * @since 1.0
 */
class HospitalHelper
{
	/**
	 * getTargetLink
	 *
	 * @param   string  $path
	 *
	 * @return  string
	 */
	public static function getImageSuffix($path)
	{
		if (preg_match('/\-reserve\./i', $path))
		{
			return 'reserve';
		}
		elseif (preg_match('/\-form\./i', $path))
		{
			return 'form';
		}

		return '';
	}
}
