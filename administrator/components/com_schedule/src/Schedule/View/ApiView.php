<?php
/**
 * Part of ihealth-schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\View;

use Windwalker\View\Json\AbstractJsonView;
use Schedule\Json\JsonResponse;

/**
 * Class ApiView
 *
 * @since 1.0
 */
class ApiView extends AbstractJsonView
{
	/**
	 * Method to render the view.
	 *
	 * We just return JSON string for Joomla to respond it.
	 *
	 * @return  string  The rendered view.
	 *
	 * @throws  \RuntimeException
	 */
	public function doRender()
	{
		return JsonResponse::response($this->data->toArray());
	}
}
