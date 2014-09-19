<?php
/**
 * Part of SMS CMF project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Schedule\Json\JsonResponse;
use Schedule\Table\Table;

/**
 * Class PlgSystemDev
 *
 * @since 1.0
 */
class PlgSystemDev extends JPlugin
{
	/**
	 * Property self.
	 *
	 * @var  PlgSystemDev
	 */
	public static $self;

	/**
	 * Constructor
	 *
	 * @param  object  $subject The object to observe
	 * @param  array   $config  An array that holds the plugin configuration
	 */
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
		$this->app = JFactory::getApplication();

		self::$self = $this;
	}

	/**
	 * Get self object.
	 *
	 * @return  mixed
	 */
	public static function getInstance()
	{
		return self::$self;
	}

	// System Events
	// ======================================================================================

	/**
	 * onAfterInitialise
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		$uri = JURI::getInstance();
		$user = JFactory::getUser();
		$query = $uri->getQuery();
		$input = $app->input;

		// Config override
		if ($app->isAdmin() && $input->get('option') == 'com_config' && $input->get('component') == 'com_schedule')
		{
			include_once JPATH_ADMINISTRATOR . '/components/com_schedule/src/init.php';

			if ($input->get('task'))
			{
				JObserverMapper::addObserverClassToClass(
					'Schedule\\Table\\Observer\\ConfigObserver',
					'JTableExtension',
					array('typeAlias' => 'com_config.schedule')
				);
			}
			else
			{
				\Schedule\Config\ConfigHelper::storeRuntime();
			}
		}

		if ($this->setupApiRoute())
		{
			return;
		}

		// Redirect to component
		if (empty($query) && !$user->guest)
		{
			$app->redirect('index.php?option=com_schedule');

			exit();
		}

		// Site to Admin
		if ($app->isSite())
		{
			$easyset = JPluginHelper::getPlugin('system', 'asikart_easyset');
			$params = new JRegistry(json_decode($easyset->params));

			$app->redirect(JUri::root() . '/administrator/index.php?option=com_schedule&' . $params->get('adminSecureCode', ''));

			exit();
		}
	}

	/**
	 * Setup up API route rule
	 *
	 * @return  bool  After setup an API route, return true. Return false when the route is not an API.
	 */
	protected function setupApiRoute()
	{
		$app   = JFactory::getApplication();
		$uri   = JURI::getInstance();
		$path  = $uri->getPath();
		$root  = JUri::root(true);
		$route = substr($path, strlen($root));

		include_once JPATH_ADMINISTRATOR . '/components/com_schedule/src/init.php';

		// Start using json format if uri path begin with `/api`
		if (strpos($route, '/api') === 0)
		{
			JsonResponse::registerErrorHandler();

			$input = $app->input;

			// Set Format to JSON
			$input->set('format', 'json');

			// Store JDocumentJson to Factory
			\JFactory::$document = JDocument::getInstance('json');

			$router = $app->getRouter();

			// Attach a hook to Router
			$router->attachParseRule(
				function(JRouter $router, JUri $uri)
				{
					$path = $uri->getPath();

					// No path & method, return 404.
					if (trim($path, '/') == 'api')
					{
						throw new InvalidArgumentException('No method.', 404);
					}

					// Direct our URI to schedule
					$path = 'component/schedule/' . $path;
					$uri->setPath($path);
					$uri->setVar('format', 'json');

					return array();
				}
			);

			return true;
		}

		return false;
	}

	/**
	 * onAfterRoute
	 *
	 * @return  void
	 */
	public function onAfterRoute()
	{
	}

	/**
	 * onAfterDispatch
	 *
	 * @return  void
	 */
	public function onAfterDispatch()
	{
	}

	/**
	 * onBeforeRender
	 *
	 * @return  void
	 */
	public function onBeforeRender()
	{
	}

	/**
	 * onAfterRender
	 *
	 * @return  void
	 */
	public function onAfterRender()
	{
	}
}
