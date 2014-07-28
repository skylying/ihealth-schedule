<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Model\Traits;

use Windwalker\Model\Helper\QueryHelper;

/**
 * Class ExtendedListModelTrait
 *
 * @since 1.0
 */
trait ExtendedListModelTrait
{
	/**
	 * getQueryHelper
	 *
	 * @param bool $forceNew
	 *
	 * @return  QueryHelper
	 */
	public function getQueryHelper($forceNew = false)
	{
		return $this->container->get('model.' . $this->getName() . '.helper.query');
	}

	/**
	 * mergeFilterFields
	 *
	 * @return  static
	 */
	public function mergeFilterFields()
	{
		$this->filterFields = array_merge($this->filterFields, $this->getQueryHelper()->getFilterFields());

		return $this;
	}

	/**
	 * getSelectFields
	 *
	 * @param int $options
	 *
	 * @return  static
	 */
	public function getSelectFields($options = QueryHelper::COLS_WITH_FIRST)
	{
		$this->getQueryHelper()->getSelectFields($options);

		return $this;
	}
}
