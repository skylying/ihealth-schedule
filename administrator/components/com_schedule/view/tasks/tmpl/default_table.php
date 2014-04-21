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
<table id="taskList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--TASK DATE-->
	<th width="20%" class="nowrap center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_TASK_TITLE_LIST_DATE', 'task.date'); ?>
	</th>

	<!--SENDER NAME-->
	<th width="20%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_TASK_TITLE_LIST_SENDER_NAME', 'task.sender_name'); ?>
	</th>

	<!--STATE-->
	<th width="5%" class="nowrap center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_TASK_TITLE_LIST_STATUS', 'task.status'); ?>
	</th>

	<!--LANGUAGE-->
	<th width="64%" class="center">
		<?php echo JText::_('COM_SCHEDULE_TASK_TITLE_LIST_PRINT'); ?>
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
	<tr class="task-row" sortable-group-id="<?php echo $item->catid; ?>">
		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->task_id); ?>
		</td>

		<!--TASK DATE-->
		<td class="center">
			<?php
			$weekday = JDate::getInstance( $item->date );
			echo  $weekday->format('Y-m-d') . '(' . $weekday->dayToString($weekday->dayofweek) . ')';
			?>
		</td>

		<!--SENDER NAME-->
		<td class="nowrap quick-edit-wrap">
			<div class="item-title center">
				<?php echo $item->sender_name; ?>
			</div>
		</td>

		<!--STATE-->
		<td class="center">
			<div class="btn-group">
				<!-- STATE BUTTON -->
				<?php echo $grid->state() ?>

				<?php //\Schedule\Helper\ToggleHelper::toggleState(); ?>

				<!-- CHANGE STATE DROP DOWN -->
				<?php echo $this->loadTemplate('dropdown'); ?>
			</div>
		</td>

		<!--PRINT-->
		<td class="center">
			<a href="print-preview" class="btn btn-info">
				<span class="glyphicon glyphicon-print"></span>
				列印
			</a>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
