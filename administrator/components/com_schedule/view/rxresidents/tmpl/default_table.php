<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Data\Data;

// No direct access
defined('_JEXEC') or die;

// Prepare script
JHtmlBehavior::multiselect('adminForm');

/**
 * Prepare data for this template.
 *
 * @var $container Windwalker\DI\Container
 * @var $data      Windwalker\Data\Data
 * @var $asset     Windwalker\Helper\AssetHelper
 * @var $grid      Windwalker\View\Helper\GridHelper
 * @var $date      \JDate
 */
$container = $this->getContainer();
$asset     = $container->get('helper.asset');
$grid      = $data->grid;
$date      = $container->get('date');

// Set order script.
$grid->registerTableSort();
?>

<!-- LIST TABLE -->
<table id="rxresidentList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!--CHECKBOX-->
	<th class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('編號', 'rxresident.id'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('散客姓名', 'rxresident.customer_name'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('所屬會員', 'rxresident.member_name'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('上傳方式', 'rxresident.method'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('處方箋狀態', 'rxresident.received'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('電聯紀錄', 'rxresident.called'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('就醫日期', 'rxresident.see_dr_date'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('可調劑次數', 'rxresident.times'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('宅配次數', 'rxresident.deliver_nths'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('新增人', 'user.created_by'); ?>
	</th>

	<th class="center">
		<?php echo $grid->sortTitle('最後修改人', 'user.modified_by'); ?>
	</th>
</tr>
</thead>

<!--PAGINATION-->
<tfoot>
<tr>
	<td colspan="15">
		<div class="pull-left">
			<?php echo $data->pagination->getListFooter(); ?>
		</div>
	</td>
</tr>
</tfoot>

<!-- TABLE BODY -->
<tbody>
<?php foreach ($data->items as $i => $item)
	:
	// Prepare data
	$item = new Data($item);

	// Prepare item for GridHelper
	$grid->setItem($item, $i);
	?>
	<tr class="prescription-row">

		<!-- checkbox -->
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->prescription_id); ?>
		</td>

		<!-- id -->
		<td>
			<?php echo $this->escape($item->id); ?>
		</td>

		<!-- 散客名稱 -->
		<td>
			<?php echo $this->escape($item->customer_name); ?>
		</td>

		<!-- 所屬會員 -->
		<td>
			<?php echo $this->escape($item->member_name); ?>
		</td>

		<!-- 上傳方式 -->
		<td>
			<?php echo $this->escape($item->method); ?>
		</td>

		<!-- 處方簽狀態 -->
		<td>
			<?php echo ($item->received) ? "已取得" : "未取得"; ?>
		</td>

		<!-- 電聯狀態 -->
		<td>
			<?php echo ($item->called) ? "已電聯" : "未電聯"; ?>
		</td>

		<!-- 就醫日期 -->
		<td>
			<?php echo $this->escape($item->see_dr_date); ?>
		</td>

		<!-- 可調劑次數 -->
		<td>
			<?php echo $this->escape($item->times); ?>
		</td>

		<!-- 宅配次數 -->
		<td>
			<?php echo $this->escape($item->deliver_nths); ?>
		</td>

		<!-- 新增人 -->
		<td>
			<?php echo $this->escape($item->author_name); ?>
		</td>

		<!-- 修改人 -->
		<td>
			<?php echo $this->escape($item->modifier_name); ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>