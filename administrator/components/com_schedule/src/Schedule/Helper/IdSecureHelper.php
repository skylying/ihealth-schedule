<?php

namespace Schedule\Helper;

/**
 * The CustomerHelper class.
 *
 * @since  {DEPLOY_VERSION}
 */
class IdSecureHelper
{
	/**
	 * secureIdnumber
	 *
	 * @param string $id
	 *
	 * @return  string
	 */
	public static function secureIdnumber($id)
	{
		$id = substr($id, 0, -4);

		return $id . '****';
	}
}
