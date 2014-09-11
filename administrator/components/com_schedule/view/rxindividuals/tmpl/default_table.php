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

	<!-- EDIT -->
	<th width="3%" class="center nowrap">
		編輯
	</th>

	<!-- 編號 -->
	<th class="center">
		<?php echo $grid->sortTitle('編號', 'rxindividual.id'); ?>
	</th>

	<!-- 過期排程 -->
	<th class="center">
		過期排程
	</th>

	<!-- 散客姓名 -->
	<th>
		<?php echo $grid->sortTitle('散客姓名', 'rxindividual.customer_name'); ?>
	</th>

	<!-- 所屬會員 -->
	<th>
		<?php echo $grid->sortTitle('所屬會員', 'rxindividual.member_name'); ?>
	</th>

	<!-- 上傳方式 -->
	<th>
		<?php echo $grid->sortTitle('上傳方式', 'rxindividual.method'); ?>
	</th>

	<!-- 處方箋狀態 -->
	<th class="center">
		<?php echo $grid->sortTitle('處方箋狀態', 'rxindividual.received'); ?>
	</th>

	<!-- 電聯紀錄 -->
	<th class="center">
		<?php echo $grid->sortTitle('電聯紀錄', 'rxindividual.called'); ?>
	</th>

	<!-- 就醫日期 -->
	<th class="center">
		<?php echo $grid->sortTitle('就醫日期', 'rxindividual.see_dr_date'); ?>
	</th>

	<!-- 可調劑次數 -->
	<th class="center">
		<?php echo $grid->sortTitle('可調劑次數', 'rxindividual.times'); ?>
	</th>

	<!-- 宅配次數 -->
	<th>
		<?php echo $grid->sortTitle('宅配次數', 'rxindividual.deliver_nths'); ?>
	</th>

	<!-- 新增人 -->
	<th>
		<?php echo $grid->sortTitle('新增人', 'rxindividual.created_by'); ?>
	</th>

	<!-- 最後修改人 -->
	<th>
		<?php echo $grid->sortTitle('最後修改人', 'rxindividual.modified_by'); ?>
	</th>

	<!-- 已列印 -->
	<th class="center">
		<?php echo $grid->sortTitle('已列印', 'rxindividual.printed'); ?>
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

		<td class="center">
			<?php echo \Schedule\Helper\UiHelper::editButton('rxindividual', $item->id); ?>
		</td>

		<!-- id -->
		<td class="center">
			<?php echo $this->escape($item->id); ?>
		</td>

		<!-- 過期排程 -->
		<td class="center">
			<?php
			$output = '';

			if (empty($item->expired_nths))
			{
				$output = '<span class="btn btn-success">正常</span>';
			}
			else
			{
				foreach (explode(',', $item->expired_nths) as $nth)
				{
					$output .= '<span class="label label-danger">' . substr($nth, 0, 1) . '</span> ';
				}
			}

			echo $output;
			?>
		</td>

		<!-- 散客名稱 -->
		<td class="center">
			<?php echo Schedule\Helper\UiHelper::foreignLink('customer', $item->customer_name, $item->customer_id, '', array('target' => '_blank'));?>
		</td>

		<!-- 所屬會員 -->
		<td class="center">
			<?php
				$members = empty($item->member_json) ? array() : json_decode("[" . $item->member_json . "]");
				foreach ($members as $member)
				{
					echo Schedule\Helper\UiHelper::foreignLink('member', $member->name, $member->id, '', array('target' => '_blank'));
				}
			?>
		</td class="center">

		<!-- 上傳方式 -->
		<td class="center">
			<?php echo $this->escape(Jtext::_('COM_SCHEDULE_RXINDIVIDUAL_PRINT_' . $item->method)); ?>
		</td>

		<!-- 處方簽狀態 -->
		<td class="center">
			<?php echo ($item->received) ? "已取得" : "未取得"; ?>
		</td>

		<!-- 電聯狀態 -->
		<td class="center">
			<?php echo ($item->called) ? "已電聯" : "未電聯"; ?>
		</td>

		<!-- 就醫日期 -->
		<td class="center">
			<?php echo $this->escape($item->see_dr_date); ?>
		</td>

		<!-- 可調劑次數 -->
		<td class="center">
			<?php echo $this->escape($item->times); ?>
		</td>

		<!-- 宅配次數 -->
		<td class="center">
			<?php echo $this->escape($item->deliver_nths); ?>
		</td>

		<!-- 新增人 -->
		<td class="center">
			<?php echo $this->escape($item->author_name); ?>
		</td>

		<!-- 修改人 -->
		<td class="center">
			<?php echo $this->escape($item->modifier_name); ?>
		</td>

		<!-- 已列印 -->
		<td class="center">
			<span class="glyphicon glyphicon-<?php echo ($item->printed ? 'ok' : 'remove'); ?>"
				<?php echo ($item->printed ? ' style="color:green;"' : ' style="color:red;"'); ?>>
			</span>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
