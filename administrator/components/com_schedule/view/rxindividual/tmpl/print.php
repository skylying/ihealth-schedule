<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
	<div class="col-lg-12 center">
		<a class="btn btn-default btn-info" onclick="window.print();">
			<i class="glyphicon glyphicon-print"> 列印 </i>
		</a>
		<a class="btn btn-default btn-danger center" onclick="window.close();">
			<i class="glyphicon glyphicon-remove-circle">
				關閉視窗
			</i>
		</a>
	</div>
	<div class="col-lg-12 center">
		<h2>這是
			<?php echo $data->item->customer_name; ?>
		</h2>
	</div>
<div>

