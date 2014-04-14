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
<table id="senderList" class="table table-striped adminlist">

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

	<!--ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('JGLOBAL_FIELD_ID_LABEL', 'sender.id'); ?>
	</th>

	<!--NAME-->
	<th class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_SENDER_ITEM_NAME', 'sender.name'); ?>
	</th>

	<!--NAME-->
	<th width="10%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_SENDER_ITEM_NOTE', 'sender.note'); ?>
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
	<tr class="sender-row" sortable-group-id="<?php echo $item->catid; ?>">
		<!-- DRAG SORT -->
		<td class="order nowrap center hidden-phone">
			<?php echo $grid->dragSort(); ?>
		</td>

		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->sender_id); ?>
		</td>

		<!--ID-->
		<td class="center">
			<?php echo (int) $item->id; ?>
		</td>

		<!--NAME-->
		<td class="nowrap has-context quick-edit-wrap">
			<div class="item-title center">
				<?php
					$query = array(
						'option' => 'com_schedule',
						'view' => 'sender',
						'layout' => 'edit',
						'id'  => $item->id
					);
				?>
				<a href="<?php echo JRoute::_("index.php?". http_build_query($query)); ?>">
				<?php echo $this->escape($item->name); ?></a>
			</div>
		</td>

		<!--NOTE-->
		<td class="center">
			<?php echo $this->escape($item->note); ?>
		</td>




	</tr>
<?php endforeach; ?>
</tbody>
</table>
