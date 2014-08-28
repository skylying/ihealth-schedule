<?php

namespace SMS;

use Aws\S3\S3Client;

/**
 * Class S3
 */
class S3 implements StorageInterface
{
	/**
	 * S3 client
	 *
	 * @var  S3Client
	 */
	protected $client;

	/**
	 * Configuration
	 *
	 * @var  array
	 */
	protected $config;

	/**
	 * Constructor
	 *
	 * @param   array  $config  Configurations:
	 *                          - key: API KEY
	 *                          - secret: API SECRET
	 *                          - bucket: S3 bucket name
	 *                          - region: S3 bucket region
	 *                          - hash: (optional) True to enable filename hash, false to disable.
	 *                          		Default is false
	 *                          - prefix: (optional) remote storage
	 *
	 * @throws  \Exception
	 */
	public function __construct(array $config)
	{
		$this->setConfig($config);

		// Create S3Client instance
		$this->client = S3Client::factory(
			array(
				'key'    => $config['key'],
				'secret' => $config['secret'],
				'bucket' => $config['bucket'],
				'region' => $config['region'],
			)
		);
	}

	/**
	 * Put file to S3
	 *
	 * @param   string  $localFilePath   Local file path
	 * @param   string  $remoteFilePath  S3 file path
	 *
	 * @return  array
	 *          - ETag: Entity tag for the uploaded object.
	 *          - Expiration: If the object expiration is configured, this will contain the expiration date (expiry-date)
	 *            and rule ID (rule-id). The value of rule-id is URL encoded.
	 *          - ServerSideEncryption: The Server-side encryption algorithm used when storing this object in S3.
	 *          - VersionId: Version of the object.
	 *          - RequestId: Request ID of the operation
	 *          - ObjectURL: URL of the uploaded object
	 *
	 * @see http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.S3.S3Client.html#_putObject
	 */
	public function put($localFilePath, $remoteFilePath)
	{
		// Hash file name
		if ($this->config['hash'])
		{
			$dir = dirname($remoteFilePath);
			$ext = pathinfo($localFilePath, PATHINFO_EXTENSION);

			$remoteFilePath = $this->config['prefix'] . '/' . $dir . '/' .
				sha1_file($localFilePath) . '.' . $ext;
		}
		else
		{
			$dir = dirname($remoteFilePath);

			$remoteFilePath = $this->config['prefix'] . '/' . $dir . '/' .
				basename($localFilePath);
		}

		$result = $this->client->putObject(
			array(
				'ACL' => 'public-read',
				'Bucket' => $this->config['bucket'],
				'Key' => $remoteFilePath,
				'SourceFile' => $localFilePath,
			)
		);

		// We can poll the object until it is accessible
		$this->client->waitUntilObjectExists(
			array(
				'Bucket' => $this->config['bucket'],
				'Key'    => $remoteFilePath,
			)
		);

		return $result->getAll();
	}

	/**
	 * Delete S3 file
	 *
	 * @param   string  $remoteFilePath  S3 file path
	 *
	 * @return  \Guzzle\Service\Resource\Model
	 */
	public function delete($remoteFilePath)
	{
		$result = $this->client->deleteObject(
			array(
				'Bucket' => $this->config['bucket'],
				'Key' => $remoteFilePath,
			)
		);

		return $result;
	}

	/**
	 * Get configuration
	 *
	 * @return  array
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * Setup configuration
	 *
	 * @param   array  $config
	 *
	 * @return  void
	 *
	 * @throws  \Exception
	 */
	public function setConfig(array $config)
	{
		// Check required configuration
		foreach (array('key', 'secret', 'bucket', 'region') as $name)
		{
			if (! isset($config[$name]))
			{
				throw new \Exception(sprintf('configuration %s is not exist.', $name));
			}
		}

		$config['hash'] = empty($config['hash']) ? false : (bool) $config['hash'];
		$config['prefix'] = empty($config['prefix']) ? false : (bool) $config['prefix'];

		$config['prefix'] = trim($config['prefix'], ' /');

		$this->config = $config;
	}

	/**
	 * Enable file hash
	 *
	 * @return  void
	 */
	public function enableHash()
	{
		$this->config['hash'] = true;
	}

	/**
	 * Disable file hash
	 *
	 * @return  void
	 */
	public function disableHash()
	{
		$this->config['hash'] = false;
	}
}
