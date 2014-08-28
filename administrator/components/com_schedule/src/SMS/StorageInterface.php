<?php

namespace SMS;

/**
 * Interface StorageInterface
 */
interface StorageInterface
{
	/**
	 * Put file to storage
	 *
	 * @param   string  $localFilePath    Local file path
	 * @param   string  $storageFilePath  Storage file path
	 *
	 * @return  mixed
	 */
	public function put($localFilePath, $storageFilePath);

	/**
	 * Delete storage file
	 *
	 * @param   string  $storageFilePath  Storage file path
	 *
	 * @return  mixed
	 */
	public function delete($storageFilePath);
}
