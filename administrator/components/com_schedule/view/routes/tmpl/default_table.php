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
<table id="routeList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('JGRID_HEADING_ID', 'route.id'); ?>
	</th>

	<!--CITY-->
	<th width="5%" class="nowrap center">
		<?php echo $grid->sortTitle('縣市', 'route.city_title'); ?>
	</th>

	<!--AREA-->
	<th width="5%" class="nowrap center">
		<?php echo $grid->sortTitle('區域', 'route.area_title'); ?>
	</th>

	<!--WEEKDAY-->
	<th width="5%" class="nowrap center">
		<?php echo $grid->sortTitle('外送日', 'route.weekday'); ?>
	</th>

	<!--SENDER-->
	<th width="5%" class="nowrap center">
		<?php echo $grid->sortTitle('外送藥師', 'route.sender_name'); ?>
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
	<tr class="route-row" sortable-group-id="<?php echo $item->catid; ?>">

		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->route_id); ?>
		</td>

		<!--ID-->
		<td class="center">
			<a href="<?php echo JRoute::_('index.php?option=com_schedule&view=route&layout=edit&id=' . $item->id); ?>"><?php echo $item->id; ?></a>
		</td>

		<!--CITY-->
		<td class="center">
			<?php echo $item->city_title;?>
		</td>

		<!--AREA-->
		<td class="center">
			<?php echo $item->area_title;?>
		</td>

		<!--WEEKDAY-->
		<td class="center">
			<?php echo JText::_($item->weekday);?>
		</td>

		<!--SENDER-->
		<td class="center">
			<?php echo $item->sender_name;?>
		</td>


	</tr>
<?php endforeach; ?>
</tbody>
</table>
