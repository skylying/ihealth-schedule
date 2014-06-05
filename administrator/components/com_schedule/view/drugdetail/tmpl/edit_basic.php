<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$container = $this->getContainer();
$asset     = $container->get('helper.asset');

JHtmlJquery::framework(true);

$asset->addJS('multi-row-handler.js');

?>
<table id="drug-details" class="table">
	<thead>
	<tr>
		<th>
			排程編號
		</th>
		<th>
			處方箋編號
		</th>
		<th>
			新增處方箋日
		</th>
		<th>
			吃完藥日
		</th>
		<th>
			所屬機構/會員
		</th>
		<th>
			縣市
		</th>
		<th>
			區域
		</th>
		<th>
			客戶
		</th>
		<th>
			完成分藥
		</th>
		<th>
			冰品
		</th>
		<th>
			自費金額
		</th>
		<th>
			最後編輯者
		</th>
	</tr>
	</thead>
	<tbody>

	</tbody>
</table>


<script id="row-template" class="hide" type="text/html">
	<?php echo $this->loadTemplate('list_row', array('group' => 'items.0hash0', 'form' => $data->form)); ?>
</script>
