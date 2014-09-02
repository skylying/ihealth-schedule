<?php

namespace Schedule\Helper;

/**
 * The IdSecureHelper class.
 *
 * @since  {DEPLOY_VERSION}
 */
class IdSecureHelper
{
	/**
	 * secureIdnumber
	 *
	 * @param string $idNumber
	 *
	 * @return  string
	 */
	public static function secureIdnumber($idNumber)
	{
		$idNumber = substr($idNumber, 0, -4);

		return $idNumber . '****';
	}
}
