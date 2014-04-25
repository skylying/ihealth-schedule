<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Data\Data;
use \Schedule\Helper\DeliveryHelper;
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
		<td colspan="5">
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
		<tr class="task-row">
			<!--CHECKBOX-->
			<td class="center">
				<?php echo JHtml::_('grid.id', $i, $item->task_id); ?>
			</td>

			<!--TASK DATE-->
			<td class="center">
				<?php
				$query = array(
					'option' => 'com_schedule',
					'view'   => 'task',
					'layout' => 'edit',
					'id'     => $item->id
				);
				?>
				<a href="<?php echo JRoute::_("index.php?" . http_build_query($query)); ?>">

					<?php
					$weekday = JDate::getInstance($item->date);
					echo sprintf('%s (%s)', $weekday->format('Y-m-d'), JText::_($weekday->format('D')));
					?>
				</a>
			</td>

			<!--SENDER NAME-->
			<td class="nowrap quick-edit-wrap">
				<div class="item-title center">
					<?php echo $item->sender_name; ?>
				</div>
			</td>

			<!--STATE-->
			<td class="center">
				<?php echo DeliveryHelper::deliveryButton($item->id, $item->status)?>
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
