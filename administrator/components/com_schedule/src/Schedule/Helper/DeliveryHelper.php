<?php

namespace Schedule\Helper;

/**
 * Class DeliveryHelper
 *
 * @since 1.0
 */
class DeliveryHelper
{
	/**
	 * Render delivery toggle button
	 *
	 * @param int $id
	 * @param int $status
	 *
	 * @return  string
	 */
	public static function deliveryButton($id, $status)
	{
		// Joomla build-in checkbox id starts with "cid0", so we set item id minus 1 to fit it
		$id--;

		if ($status == 1)
		{
			$html = '<div class="btn btn-success" onclick="return listItemTask(\'cb' . $id . '\', \'tasks.state.undelivery\')">';
			$html .= '<span class="glyphicon glyphicon-ok-circle"></span> 已外送</div>';
		}
		else
		{
			$html = '<div class="btn btn-info" onclick="return listItemTask(\'cb' . $id . '\', \'tasks.state.delivery\')">';
			$html .= '<span class="glyphicon glyphicon-pause"></span> 待外送</div>';
		}

		return $html;
	}
}
