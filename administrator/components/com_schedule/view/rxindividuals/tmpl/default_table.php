<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Data\Data;
use Schedule\Helper\UiHelper;

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
	<th class="center">
		<?php echo $grid->sortTitle('散客姓名', 'rxindividual.customer_name'); ?>
	</th>

	<!-- 所屬會員 -->
	<th class="center">
		<?php echo $grid->sortTitle('所屬會員', 'rxindividual.member_name'); ?>
	</th>

	<!-- 上傳方式 -->
	<th class="center">
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

	<!-- 新增日期 -->
	<th class="center">
		<?php echo $grid->sortTitle('新增日期', 'rxindividual.created'); ?>
	</th>

	<!-- 可調劑次數 -->
	<th class="center">
		<?php echo $grid->sortTitle('可調劑次數', 'rxindividual.times'); ?>
	</th>

	<!-- 宅配次數 -->
	<th class="center">
		<?php echo $grid->sortTitle('宅配次數', 'rxindividual.deliver_nths'); ?>
	</th>

	<!-- 新增人 -->
	<th class="center">
		<?php echo $grid->sortTitle('新增人', 'rxindividual.created_by'); ?>
	</th>

	<!-- 最後修改人 -->
	<th class="center">
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
			<?php echo UiHelper::editButton('rxindividual', $item->id); ?>
		</td>

		<!-- id -->
		<td class="center">
			<?php echo $this->escape($item->id); ?>
		</td>

		<!-- 過期排程 -->
		<td class="center">
			<?php if (empty($item->expired_nths)): ?>
				<span class="btn btn-success">正常</span>
			<?php else: ?>
				<?php foreach (explode(',', $item->expired_nths) as $nth): ?>
					<span class="label label-danger"><?php echo substr($nth, 0, 1) ?></span>
				<?php endforeach; ?>
			<?php endif; ?>
		</td>

		<!-- 散客名稱 -->
		<td class="center">
			<?php echo UiHelper::foreignLink('customer', $item->customer_name, $item->customer_id, '', array('target' => '_blank'));?>
		</td>

		<!-- 所屬會員 -->
		<td class="center">
			<?php
				$members = empty($item->member_json) ? array() : json_decode("[" . $item->member_json . "]");
				foreach ($members as $member)
				{
					echo UiHelper::foreignLink('member', $member->name, $member->id, '', array('target' => '_blank'));
				}
			?>
		</td class="center">

		<!-- 上傳方式 -->
		<td class="center">
			<?php echo $this->escape(Jtext::_('COM_SCHEDULE_RXINDIVIDUAL_PRINT_' . $item->method)); ?>
		</td>

		<!-- 處方簽狀態 -->
		<td class="center">
			<?php echo ($item->received) ? '<span class="btn btn-success">已取得</span>' : '<span class="btn btn-danger">未取得</span>'; ?>
		</td>

		<!-- 電聯狀態 -->
		<td class="center">
			<?php echo ($item->called) ? '<span class="btn btn-success">已電聯</span>' : '<span class="btn btn-danger">未電聯</span>'; ?>
		</td>

		<!-- 就醫日期 -->
		<td class="center">
			<?php echo $this->escape($item->see_dr_date); ?>
		</td>

		<!-- 新增日期 -->
		<td class="center">
			<?php echo substr($this->escape($item->created), 0, 10); ?>
		</td>

		<!-- 可調劑次數 -->
		<td class="center">
			<?php echo $this->escape($item->times); ?>
		</td>

		<!-- 宅配次數 -->
		<td class="center">
			<?php
			foreach (explode(',', $item->deliver_nths) as $nth)
			{
				echo '<span class="badge">' . substr($nth, 0, 1) . '</span> ';
			}
			?>
		</td>

		<!-- 新增人 -->
		<td class="center">
			<?php
			// Define new prescription through API
			$params = new Data(json_decode($item->params));

			if ($params->fromOfficialSite)
			{
				echo '<span class="btn btn-warning">官網客戶</span>';
			}
			else
			{
				echo $this->escape($item->author_name);
			}
			?>
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
