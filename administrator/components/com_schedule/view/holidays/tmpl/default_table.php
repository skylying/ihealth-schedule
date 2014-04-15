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
<table id="holidayList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('JGRID_HEADING_ID', 'holiday.id'); ?>
	</th>

	<!--YEAR-->
	<th width="10%" class="center">
		<?php echo $grid->sortTitle('年份', 'holiday.year'); ?>
	</th>

	<!--MONTH-->
	<th width="10%" class="center">
		<?php echo $grid->sortTitle('月份', 'holiday.month'); ?>
	</th>

	<!--DAY-->
	<th width="10%" class="center">
		<?php echo $grid->sortTitle('日期', 'holiday.day'); ?>
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
	<tr class="holiday-row" sortable-group-id="<?php echo $item->catid; ?>">

		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->holiday_id); ?>
		</td>

		<!--ID-->
		<td class="center">
			<a href="<?php echo JRoute::_('index.php?option=com_schedule&view=holiday&layout=edit&id=' . $item->id); ?>"><?php echo $item->id; ?></a>
		</td>

		<!--YEAR-->
		<td class="center">
			<?php echo $this->escape($item->year); ?>
		</td>

		<!--MONTH-->
		<td class="center">
			<?php echo $this->escape($item->month); ?>
		</td>

		<!--DAY-->
		<td class="center">
			<?php echo $this->escape($item->day); ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
