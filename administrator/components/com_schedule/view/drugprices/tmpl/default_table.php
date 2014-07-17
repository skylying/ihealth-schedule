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
<table id="drugpriceList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('JGRID_HEADING_ID', 'drugprice.id'); ?>
	</th>

	<!--EDIT-->
	<th width="1%" class="nowrap center">
		編輯
	</th>

	<!--INSTITUTE_ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('對應機構', 'drugprice.institute_id'); ?>
	</th>

	<!--CUSTOMER_ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('對應外送id', 'drugprice.task_id'); ?>
	</th>

	<!--DATE-->
	<th width="10%" class="nowrap center">
		<?php echo $grid->sortTitle('外送日期', 'drugprice.date'); ?>
	</th>

	<!--DATE-->
	<th width="10%" class="nowrap center">
		<?php echo $grid->sortTitle('單筆金額', 'drugprice.price'); ?>
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
	<tr class="drugprice-row" sortable-group-id="<?php echo $item->catid; ?>">
		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->drugprice_id); ?>
		</td>

		<!--ID-->
		<td class="center">
			<?php echo $item->id; ?>
		</td>

		<!--EDIT-->
		<td class="center">
			<?php echo \Schedule\Helper\UiHelper::editButton('drugprice', $item->id); ?>
		</td>

		<!--INSTITUTE_TITLE-->
		<td width="10%" class="center">
			<?php echo \Schedule\Helper\UiHelper::foreignLink('institute', $item->institute_short_title, $item->institute_id); ?>
		</td>

		<!--CUSTOMER_NAME-->
		<td class="center">
			<?php echo \Schedule\Helper\UiHelper::foreignLink('task', $item->task_id, $item->task_id); ?>
		</td>

		<!--DATE-->
		<td class="center">
			<?php echo $item->date; ?>
		</td>

		<!--PRICE-->
		<td class="center">
			<?php echo $item->price; ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
