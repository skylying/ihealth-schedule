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
<table id="colorList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--TITLE-->
	<th width="10%"  class="center">
		<?php echo $grid->sortTitle('JGLOBAL_TITLE', 'color.title'); ?>
	</th>

	<!--HEX block-->
	<th width="10%"  class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_COLOR_HEX_LABEL', 'color.hex'); ?>
	</th>

	<!--ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('JGRID_HEADING_ID', 'color.id'); ?>
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
	<tr class="color-row" sortable-group-id="<?php echo $item->catid; ?>">
		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->color_id); ?>
		</td>

		<!--TITLE-->
		<td class="n/owrap has-context quick-edit-wrap">
			<div class="item-title text-center">
				<!-- Checkout -->
				<?php echo $grid->checkoutButton(); ?>

				<!-- Title -->
				<?php echo $grid->editTitle(); ?>
			</div>
		</td>

		<!--Hex block-->
		<td class="center">
			<div style=" margin:0 auto; width: 25px; height: 25px; background: <?php echo $item->hex; ?>"></div>
		</td>

		<!--ID-->
		<td class="center">
			<?php echo (int) $item->id; ?>
		</td>

	</tr>
<?php endforeach; ?>
</tbody>
</table>
