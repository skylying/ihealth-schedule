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
<table id="instituteList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>

	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--Short Title-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_SHORT_TITLE', 'institute.short_title'); ?>
	</th>

	<!--Delivery Weekday-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_DELIVERY_WEEKDAY', 'institute.title'); ?>
	</th>

	<!--Last Delivery Weekday-->
	<th width="5%" class="nowrap left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_LAST_DELIVERY_WEEKDAY', 'institute.delivery_weekday'); ?>
	</th>

	<!--Color for Weekday-->
	<th width="5%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_MARK_COLOR', 'institute.color_hex'); ?>
	</th>

	<!--Sender Name-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_SENDER_NAME', 'institute.sender_name'); ?>
	</th>

	<!--Tel-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_TEL', 'institute.tel'); ?>
	</th>

	<!--City-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_CITY', 'institute.city'); ?>
	</th>

	<!--Area-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_AREA', 'institute.area'); ?>
	</th>

	<!--Address-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_ADDRESS', 'institute.address'); ?>
	</th>

	<!--ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_SERIAL', 'institute.id'); ?>
	</th>

	<!--Link To Elder List-->
	<th width="5%" class="nowrap center">
		<?php echo JText::_('COM_SCHEDULE_INSTITUTE_ELDER_LISTS')?>
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
	<tr class="institute-row" sortable-group-id="<?php echo $item->id; ?>">

		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->institute_id); ?>
		</td>

		<!--Short Title-->
		<td class="left">
			<a href="<?php echo JRoute::_('?option=com_schedule&view=institute&layout=edit&id=' . $item->id);?>"><?php echo $item->short_title;?></a>
		</td>

		<!--Delivery Weekday-->
		<td class="left">
			<?php echo JText::_('COM_SCHEDULE_WEEK_' . $item->delivery_weekday);?>
		</td>

		<!--Last Delivery Weekday-->
		<td class="left">
			<?php echo $item->delivery_weekday;?>
		</td>

		<!--Color for Weekday-->
		<td class="center">
			<span style="background-color: <?php echo $item->color_hex;?>"><?php echo $item->color_hex;?></span>
		</td>

		<!--Sender Name-->
		<td class="left">
			<?php echo $item->sender_name;?>
		</td>

		<!--Tel-->
		<td class="left">
			<?php echo $item->tel;?>
		</td>

		<!--City Title-->
		<td class="left">
			<?php echo $item->city_title;?>
		</td>

		<!--City Area-->
		<td class="left">
			<?php echo $item->area;?>
		</td>

		<!--Address-->
		<td class="left">
			<?php echo $item->address;?>
		</td>

		<!--ID-->
		<td class="center">
			<?php echo (int) $item->id;?>
		</td>

		<!--Link To Elder List-->
		<td class="center">
			<a href="#"> Link </a>
		</td>

	</tr>
<?php endforeach; ?>
</tbody>
</table>
