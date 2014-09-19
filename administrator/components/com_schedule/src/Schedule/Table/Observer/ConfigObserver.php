<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2014 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Schedule\Table\Observer;

use JObservableInterface;
use JObserverInterface;
use Joomla\Registry\Registry;
use Windwalker\Helper\PathHelper;

/**
 * The ConfigObserver class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class ConfigObserver extends \JTableObserver
{
	/**
	 * Creates the associated observer instance and attaches it to the $observableObject
	 *
	 * @param   JObservableInterface $observableObject The observable subject object
	 * @param   array                $params           Params for this observer
	 *
	 * @return  JObserverInterface
	 *
	 * @since   3.1.2
	 */
	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$typeAlias = $params['typeAlias'];

		$observer = new static($observableObject);

		return $observer;
	}

	/**
	 * Post-processor for $table->store($updateNulls)
	 * You can change optional params newTags and replaceTags of tagsHelper with method setNewTagsToAdd
	 *
	 * @param   boolean  &$result  The result of the load
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 */
	public function onAfterStore(&$result)
	{
		$params = $this->table->params;

		$params = new Registry($params);

		$configPath = PathHelper::getAdmin('com_schedule') . '/etc/runtime.yml';

		file_put_contents($configPath, $params->toString('yaml'));

		\JFactory::getApplication();
	}
}
