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
<table id="rxindividualList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!-- CHECKBOX -->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!-- 處方箋編號 -->
	<th class="center">
		<?php echo $grid->sortTitle('處方箋編號', 'rxindividual.id'); ?>
	</th>

	<!-- 機構名稱 -->
	<th class="center">
		<?php echo $grid->sortTitle('機構名稱', 'rxindividual.institute_short_title'); ?>
	</th>

	<!-- 住民姓名 -->
	<th class="center">
		<?php echo $grid->sortTitle('住民姓名', 'rxindividual.customer_name'); ?>
	</th>

	<!-- 身分證字號 -->
	<th class="center">
		<?php echo $grid->sortTitle('身分證字號', 'rxindividual.id_number'); ?>
	</th>

	<!-- 就醫日期 -->
	<th class="center">
		<?php echo $grid->sortTitle('就醫日期', 'rxindividual.see_dr_date'); ?>
	</th>

	<!-- 處方箋天數 -->
	<th class="center">
		<?php echo $grid->sortTitle('處方箋天數', 'rxindividual.period'); ?>
	</th>

	<!-- 可調劑次數 -->
	<th class="center">
		<?php echo $grid->sortTitle('可調劑次數', 'rxindividual.times'); ?>
	</th>

	<!-- 處方箋取得方式 -->
	<th class="center">
		<?php echo $grid->sortTitle('處方箋取得方式', 'rxindividual.method'); ?>
	</th>

	<!-- 新增人 -->
	<th class="center">
		<?php echo $grid->sortTitle('新增人', 'user.created_by'); ?>
	</th>

	<!-- 最後修改人 -->
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
		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->prescription_id); ?>
		</td>

		<!-- 處方箋編號 -->
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_schedule&task=rxindividual.edit.edit&id=' . $item->id); ?>">
				<?php echo $item->id; ?>
			</a>
		</td>

		<!-- 機構名稱 -->
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_schedule&task=institute.edit.edit&id=' . $item->institute_id); ?>">
				<?php echo $this->escape($item->institute_short_title); ?>
			</a>
		</td>

		<!-- 住民姓名 -->
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_schedule&task=customer.edit.edit&id=' . $item->customer_id); ?>">
				<?php echo $this->escape($item->customer_name); ?>
			</a>
		</td>

		<!-- 身分證字號 -->
		<td>
			<?php echo $this->escape($item->id_number); ?>
		</td>

		<!-- 就醫日期 -->
		<td>
			<?php echo $this->escape($item->see_dr_date); ?>
		</td>

		<!-- 處方箋天數 -->
		<td>
			<?php echo $this->escape($item->period); ?>
		</td>

		<!-- 可調劑次數 -->
		<td>
			<?php echo $this->escape($item->times); ?>
		</td>

		<!-- 處方箋取得方式 -->
		<td>
			<?php echo $this->escape($item->method); ?>
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
