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
<table id="scheduleList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!-- SORT -->
	<th width="1%" class="nowrap center hidden-phone">
		<?php echo $grid->orderTitle(); ?>
	</th>

	<!-- CHECKBOX -->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!-- schedule.id -->
	<th class="nowrap center">
		<?php echo $grid->sortTitle('處方箋編號', 'schedule.id'); ?>
	</th>

	<!-- schedule.type -->
	<th class="nowrap center">
		<?php echo $grid->sortTitle('類別', 'schedule.type'); ?>
	</th>

	<!-- schedule.institute_id | schedule.customer_id -->
	<th class="center">
		<?php echo $grid->sortTitle('所屬機構/所屬會員', 'schedule.type, schedule.institute_id, schedule.customer_id'); ?>
	</th>

	<!-- schedule.city -->
	<th class="center">
		<?php echo $grid->sortTitle('縣市', 'schedule.city'); ?>
	</th>

	<!-- schedule.area -->
	<th class="center">
		<?php echo $grid->sortTitle('區域', 'schedule.area'); ?>
	</th>

	<!-- schedule.customer_id -->
	<th class="center">
		<?php echo $grid->sortTitle('客戶', 'schedule.customer_id'); ?>
	</th>

	<!-- schedule.date -->
	<th class="center">
		<?php echo $grid->sortTitle('預計外送日', 'schedule.date'); ?>
	</th>

	<!-- route.sender_id -->
	<th class="center">
		<?php echo $grid->sortTitle('外送藥師', 'route.sender_id'); ?>
	</th>

	<!-- schedule.status -->
	<th class="center">
		<?php echo $grid->sortTitle('狀態', 'schedule.status'); ?>
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
	<tr class="schedule-row" sortable-group-id="<?php echo $item->catid; ?>">
		<!-- DRAG SORT -->
		<td class="order nowrap center hidden-phone">
			<?php echo $grid->dragSort(); ?>
		</td>

		<!-- CHECKBOX -->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>

		<!-- id -->
		<td class="center">
			<?php echo (int) $item->id; ?>
		</td>

		<!-- type -->
		<td class="center">
			<?php echo $item->type; ?>
		</td>

		<!-- customer_name | institute_name -->
		<td class="center">
			<?php echo ('individual' === $item->type ? $item->customer_name  : ''); ?>
			<?php echo ('resident' === $item->type ? $item->institute_name : ''); ?>
		</td>

		<!-- city_title -->
		<td class="center">
			<?php echo $item->city_title; ?>
		</td>

		<!-- area_title -->
		<td class="center">
			<?php echo $item->area_title; ?>
		</td>

		<!-- customer_name -->
		<td class="center">
			<?php echo $item->customer_name; ?>
		</td>

		<!-- date -->
		<td class="center">
			<?php echo $item->date; ?>
		</td>

		<!-- route_sender_name -->
		<td class="center">
			<?php echo $item->route_sender_name; ?>
		</td>

		<!-- status -->
		<td class="center">
			<?php echo $item->status; ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
