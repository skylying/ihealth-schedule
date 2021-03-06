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
<table id="drugList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('JGRID_HEADING_ID', 'drug.id'); ?>
	</th>

	<!--EDIT-->
	<th width="1%" class="nowrap center">
		編輯
	</th>

	<th width="10%" class="nowrap center">
		<?php echo $grid->sortTitle('藥品健保碼', 'drug.hicode'); ?>
	</th>

	<th width="5%" class="nowrap center">
		<?php echo $grid->sortTitle('數量', 'drug.quantity'); ?>
	</th>

	<th width="5%" class="nowrap center">
		<?php echo $grid->sortTitle('對應處方箋', 'drug.rx_id'); ?>
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
	<tr class="drug-row" sortable-group-id="<?php echo $item->catid; ?>">

		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->drug_id); ?>
		</td>

		<!--ID-->
		<td class="center">
			<?php echo $item->id; ?>
		</td>

		<!--EDIT-->
		<td class="center">
			<?php echo \Schedule\Helper\UiHelper::editButton('drug', $item->id); ?>
		</td>

		<!--HICODE-->
		<td class="center">
			<?php echo $item->hicode; ?>
		</td>

		<!--QUANTITY-->
		<td class="center">
			<?php echo $item->quantity; ?>
		</td>

		<!--RX_ID-->
		<td class="center">
			<?php echo \Schedule\Helper\UiHelper::foreignLink('rxindividual', $item->rx_id, $item->rx_id); ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
