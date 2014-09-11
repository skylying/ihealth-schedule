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

$asset->addJS('schedules/list.js');
?>

<!-- LIST TABLE -->
<table id="rxindividualList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!-- CHECKBOX -->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--EDIT BUTTON-->
	<th width="5%" class="center">
		編輯
	</th>

	<!-- 處方箋編號 -->
	<th class="center">
		<?php echo $grid->sortTitle('處方箋編號', 'rxresident.id'); ?>
	</th>

	<!-- 機構名稱 -->
	<th class="center">
		<?php echo $grid->sortTitle('機構名稱', 'rxresident.institute_short_title'); ?>
	</th>

	<!-- 住民姓名 -->
	<th class="center">
		<?php echo $grid->sortTitle('住民姓名', 'rxresident.customer_name'); ?>
	</th>

	<!-- 就醫日期 -->
	<th class="center">
		<?php echo $grid->sortTitle('就醫日期', 'rxresident.see_dr_date'); ?>
	</th>

	<!-- 新增日期 -->
	<th class="center">
		<?php echo $grid->sortTitle('新增日期', 'rxresident.created'); ?>
	</th>

	<!-- 處方箋天數 -->
	<th class="center">
		<?php echo $grid->sortTitle('處方箋天數', 'rxresident.period'); ?>
	</th>

	<!-- 可調劑次數 -->
	<th class="center">
		<?php echo $grid->sortTitle('可調劑次數', 'rxresident.times'); ?>
	</th>

	<!-- 處方箋取得方式 -->
	<th class="center">
		<?php echo $grid->sortTitle('處方箋取得方式', 'rxresident.method'); ?>
	</th>

	<!-- 新增人 -->
	<th class="center">
		<?php echo $grid->sortTitle('新增人', 'rxresident.created_by'); ?>
	</th>

	<!-- 最後修改人 -->
	<th class="center">
		<?php echo $grid->sortTitle('最後修改人', 'rxresident.modified_by'); ?>
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
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>

		<!-- EDIT BUTTON -->
		<td class="center">
			<?php echo \Schedule\Helper\UiHelper::editButton('rxresident', $item->id); ?>
		</td>

		<!-- 處方箋編號 -->
		<td class="center">
			<?php echo $item->id; ?>
		</td>

		<!-- 機構名稱 -->
		<td>
			<?php echo Schedule\Helper\UiHelper::foreignLink('institute', $item->institute_short_title, $item->institute_id, '', array('target' => '_blank')); ?>
		</td>

		<!-- 住民姓名 -->
		<td class="center">
			<?php echo Schedule\Helper\UiHelper::foreignLink('customer', $item->customer_name, $item->customer_id, '', array('target' => '_blank')); ?>
		</td>

		<!-- 就醫日期 -->
		<td class="center">
			<?php echo $this->escape($item->see_dr_date); ?>
		</td>

		<!-- 新增日期 -->
		<td class="center">
			<?php echo substr($this->escape($item->created), 0, 10); ?>
		</td>

		<!-- 處方箋天數 -->
		<td class="center">
			<?php echo $this->escape($item->period); ?>
		</td>

		<!-- 可調劑次數 -->
		<td class="center">
			<?php echo $this->escape($item->times); ?>
		</td>

		<!-- 處方箋取得方式 -->
		<td class="center">
			<?php echo $this->escape(Jtext::_('COM_SCHEDULE_RXINDIVIDUAL_PRINT_' . $item->method)); ?>
		</td>

		<!-- 新增人 -->
		<td class="center">
			<?php echo $this->escape($item->author_name); ?>
		</td>

		<!-- 修改人 -->
		<td class="center">
			<?php echo $this->escape($item->modifier_name); ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
