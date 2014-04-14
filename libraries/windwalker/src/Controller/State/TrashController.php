<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Controller\State;

/**
 * Class TrashController
 *
 * @since 1.0
 */
class TrashController extends AbstractUpdateStateController
{
	/**
	 * Property stateData.
	 *
	 * @var string
	 */
	protected $stateData = array(
		'state' => '-2'
	);

	/**
	 * Property actionText.
	 *
	 * @var string
	 */
	protected $actionText = 'TRASHED';

	/**
	 * Property allowReturn.
	 *
	 * @var  boolean
	 */
	protected $allowReturn = true;
}
