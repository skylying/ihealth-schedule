<?php

use Windwalker\Controller\Edit\SaveController;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Schedule\Table\Table;
use Schedule\Helper\ImageHelper;

/**
 * Class ScheduleControllerImageEditSave
 *
 * @since 1.0
 */
class ScheduleControllerImageEditSave extends SaveController
{
	/**
	 * preSaveHook
	 *
	 * @return  void
	 */
	protected function preSaveHook()
	{
		$this->data['id'] = $this->data['upload_id'];

		parent::preSaveHook();
	}
}
