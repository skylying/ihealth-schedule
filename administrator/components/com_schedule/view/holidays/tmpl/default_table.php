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
	<!--SORT-->
	<th width="1%" class="nowrap center hidden-phone">
		<?php echo $grid->orderTitle(); ?>
	</th>

	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--STATE-->
	<th width="5%" class="nowrap center">
		<?php echo $grid->sortTitle('JSTATUS', 'holiday.state'); ?>
	</th>

	<!--YEAR-->
	<th width="10%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_HOLIDAY_YEAR_LABEL', 'holiday.year'); ?>
	</th>

	<!--MONTH-->
	<th width="10%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_HOLIDAY_MONTH_LABEL', 'holiday.month'); ?>
	</th>

	<!--DAY-->
	<th width="10%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_HOLIDAY_DAY_LABEL', 'holiday.day'); ?>
	</th>

	<!--ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('JGRID_HEADING_ID', 'holiday.id'); ?>
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
		<!-- DRAG SORT -->
		<td class="order nowrap center hidden-phone">
			<?php echo $grid->dragSort(); ?>
		</td>

		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->holiday_id); ?>
		</td>

		<!--STATE-->
		<td class="center">
			<div class="btn-group">
				<!-- STATE BUTTON -->
				<?php echo $grid->state() ?>

				<!-- CHANGE STATE DROP DOWN -->
				<?php echo $this->loadTemplate('dropdown'); ?>
			</div>
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

		<!--ID-->
		<td class="center">
			<a href="<?php echo JRoute::_('index.php?option=com_schedule&view=holiday&layout=edit&id=' . $item->id); ?>"><?php echo $item->id; ?></a>
		</td>

	</tr>
<?php endforeach; ?>
</tbody>
</table>
