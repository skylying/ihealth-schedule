<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Json;

/**
 * Class JsonResponse
 *
 * @since 1.0
 */
abstract class JsonResponse
{
	/**
	 * Response content as json.
	 *
	 * Using JResponseJson to wrap our content. It contains some information like success or messages.
	 *
	 * @param   mixed  $content
	 * @param   bool   $toString
	 *
	 * @return  string
	 */
	public static function response($content = null, $toString = true)
	{
		$json = new \JResponseJson($content);

		if ($toString)
		{
			return (string) $json;
		}

		echo $json;

		return true;
	}

	/**
	 * The error handler for API.
	 *
	 * @param   int     $errNo
	 * @param   string  $errStr
	 * @param   string  $errFile
	 * @param   int     $errLine
	 * @param   mixed   $errContext
	 *
	 * @throws  \ErrorException
	 * @return  void
	 */
	public static function error($errNo ,$errStr ,$errFile, $errLine ,$errContext)
	{
		$content = sprintf('%s. File: %s (line: %s)', $errStr, $errFile, $errNo);

		throw new \ErrorException($content, $errNo, 1, $errFile, $errLine);
	}

	/**
	 * The exception handler for API.
	 *
	 * @param   \Exception  $exception
	 *
	 * @return  void
	 */
	public static function exception(\Exception $exception)
	{
		$response = new \JResponseJson(null, $exception->getMessage(), true);

		$response->success = false;

		$response->code = $exception->getCode();

		if (JDEBUG)
		{
			$response->backtrace = $exception->getTrace();
		}

		$app = \JFactory::getApplication();
		$doc = \JFactory::getDocument();

		$app->setBody($doc->setBuffer($response)->render());

		$app->setHeader('Content-Type', $doc->getMimeEncoding() . '; charset=' . $doc->getCharset());

		echo $app->toString();

		jexit();
	}

	/**
	 * registerErrorHandler
	 *
	 * @return  void
	 */
	public static function registerErrorHandler()
	{
		restore_error_handler();
		restore_exception_handler();

		set_error_handler(array(__CLASS__, 'error'));
		set_exception_handler(array(__CLASS__, 'exception'));
	}
}
