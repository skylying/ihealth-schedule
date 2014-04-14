<?php
/**
 * Part of SMS CMF project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

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
		// $method = $app->input->getMethod();

		$easyset = JPluginHelper::getPlugin('system', 'asikart_easyset');
		$params = new JRegistry(json_decode($easyset->params));

		// Redirect to component
		if (empty($query) && !$user->guest)
		{
			$app->redirect('index.php?option=com_schedule');

			exit();
		}

		// Site to Admin
		if ($app->isSite())
		{
			$app->redirect(JUri::root() . '/administrator/index.php?option=com_schedule&' . $params->get('adminSecureCode', ''));

			exit();
		}
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