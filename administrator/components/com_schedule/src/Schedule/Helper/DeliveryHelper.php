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
		if ($status == 1)
		{
			// 原本已外送可以改回待外送的 js, 先暫時註解掉, 避免 task 撈出一堆空白排程
			// $html = '<div class="btn btn-success" onclick="return listItemTask(\'cb' . $id . '\', \'tasks.state.undelivery\')">';
			$html = '<div class="btn btn-success" onclick="alert(\'您無法將已外送排程改回待外送\')">';
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
