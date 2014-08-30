<?php

namespace SMS;

/**
 * Class StorageFactory
 */
abstract class StorageFactory
{
	/**
	 * factory
	 *
	 * @param string $type
	 * @param array  $config
	 *
	 * @return  null|S3
	 */
	public static function factory($type, $config)
	{
		$type = strtoupper($type);

		switch ($type)
		{
			case 'S3':
				return new S3($config);
		}

		return null;
	}
}
