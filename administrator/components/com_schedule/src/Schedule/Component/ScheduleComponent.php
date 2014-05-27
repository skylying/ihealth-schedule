<?php

namespace Schedule\Component;

use Schedule\Provider\ScheduleProvider;
use Windwalker\Component\Component;
use Windwalker\Debugger\Debugger;
use Windwalker\Helper\LanguageHelper;
use Windwalker\Helper\ProfilerHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ScheduleComponent
 *
 * @since 1.0
 */
abstract class ScheduleComponent extends Component
{
	/**
	 * Property name.
	 *
	 * @var string
	 */
	protected $name = 'Schedule';

	/**
	 * prepare
	 *
	 * @return  void
	 */
	protected function prepare()
	{
		if (JDEBUG && 'html' == \JFactory::getDocument()->getType())
		{
			Debugger::registerWhoops();
		}

		// Register provider
		$this->container->registerServiceProvider(new ScheduleProvider);

		// Load language
		$lang = $this->container->get('language');

		LanguageHelper::loadAll($lang->getTag(), $this->option);

		// Load asset
		$asset = $this->container->get('helper.asset');

		$asset->windwalker();

		parent::prepare();
	}

	/**
	 * postExecute
	 *
	 * @param mixed $result
	 *
	 * @return  mixed
	 */
	protected function postExecute($result)
	{
		// Debug profiler
		if (JDEBUG)
		{
			$type = \JFactory::getDocument()->getType();

			if ('html' == $type)
			{
				$result .= "<hr />" . ProfilerHelper::render('Windwalker', true);
			}
			elseif ('json' == $type)
			{
				/* Uncomment if you need this.

				$result = json_decode($result);

				$profiler = \JProfiler::getInstance('Windwalker');

				$result->profiler = $profiler->getBuffer();

				$result = json_encode($result);
				*/
			}
		}

		return parent::postExecute($result);
	}
}
