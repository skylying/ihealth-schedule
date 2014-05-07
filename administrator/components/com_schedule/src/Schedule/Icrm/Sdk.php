<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Icrm;

use Joomla\Registry\Registry;
use Windwalker\DI\Container;

/**
 * Class Sdk
 *
 * @since 1.0
 */
class Sdk
{
	/**
	 * Property uri.
	 *
	 * @var  \JUri
	 */
	protected $uri = null;

	/**
	 * Path prefix of api request, it means our api server path.
	 *
	 * @var  string
	 */
	protected $pathPrefix = '';

	/**
	 * Property container.
	 *
	 * @var  \Windwalker\DI\Container
	 */
	protected $container = null;

	/**
	 * Property dataClass.
	 *
	 * @var  string
	 */
	protected $dataClass = '\\Joomla\\Registry\\Registry';

	/**
	 * Class init.
	 *
	 * @param \JUri|string $uri       URI storage.
	 * @param Container    $container DI container.
	 */
	public function __construct($uri = null, Container $container = null)
	{
		if (!($uri instanceof \JUri))
		{
			$uri = new \JUri($uri);
		}

		$this->uri = $uri;

		$this->pathPrefix = '/' . trim($uri->getPath(), '/');

		$this->container = $container ? : Container::getInstance();
	}

	/**
	 * Execute an api request.
	 *
	 * @param string $path   The API method, example: `facility/39`.
	 * @param array  $query  Query of this request.
	 * @param string $method Http method.
	 *
	 * @throws \DomainException
	 * @return  \Joomla\Registry\Registry Resolved result object.
	 */
	public function execute($path, $query = array(), $method = 'get')
	{
		// Reset URI
		$this->uri->setPath('');
		$this->uri->setQuery(array());

		// Check method
		if (!in_array(strtolower($method), ['get', 'post', 'pull', 'delete']))
		{
			throw new \DomainException('Method: ' . $method . ' not support.');
		}

		// Set path to uri
		$this->uri->setPath($this->pathPrefix . '/' . $path);

		// Prepare Http object
		$http = \JHttpFactory::getHttp(new \JRegistry, 'Curl');

		try
		{
			if ($method == 'get')
			{
				$this->uri->setQuery($query);

				$result = $http->get((string) $this->uri);
			}
			else
			{
				$result = $http->$method((string) $this->uri, $query);
			}
		}

		/*
		 * Http request fail, we just alert user, don't lock the screen.
		 * Generic exception we let up-level to fetch it.
		 */
		catch (\RuntimeException $e)
		{
			$app = \JFactory::getApplication();

			$app->enqueueMessage($e->getMessage(), 'error');

			return $this->resolveResult('');
		}

		return $this->resolveResult($result->body);
	}

	/**
	 * Resolve the JSON Result
	 *
	 * @param string $result JSON result.
	 *
	 * @throws \RuntimeException
	 * @return \Joomla\Registry\Registry
	 */
	protected function resolveResult($result)
	{
		/** @var $app \JApplicationCms */
		$app    = $this->container->get('app');

		$result = json_decode($result);

		if (!$result && JDEBUG)
		{
			$app->enqueueMessage('API return value not JSON.', 'warning');

			return $this->wrapData(null);
		}

		$result = new Registry($result);

		// Success retrun.
		if ($result['success'])
		{
			return $this->wrapData($result['data']);
		}

		// Not success, raise warning.
		$app->enqueueMessage($result['message']);

		if (JDEBUG)
		{
			throw new \RuntimeException($result['message']);
		}

		return $this->wrapData($result['data']);
	}

	/**
	 * Wrap result data.
	 *
	 * @param mixed $data Data to wrap.
	 *
	 * @return  mixed Wrapped data.
	 */
	protected function wrapData($data)
	{
		return new $this->dataClass($data);
	}

	/**
	 * getUri
	 *
	 * @return  \JUri
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * setUri
	 *
	 * @param   \JUri $uri
	 *
	 * @return  Sdk  Return self to support chaining.
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;

		return $this;
	}

	/**
	 * getPathPrefix
	 *
	 * @return  string
	 */
	public function getPathPrefix()
	{
		return $this->pathPrefix;
	}

	/**
	 * setPathPrefix
	 *
	 * @param   string $pathPrefix
	 *
	 * @return  Sdk  Return self to support chaining.
	 */
	public function setPathPrefix($pathPrefix)
	{
		$this->pathPrefix = $pathPrefix;

		return $this;
	}
}
