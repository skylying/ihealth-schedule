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
<table id="instituteList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>

	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--ID-->
	<th width="1%" class="nowrap center" colspan="2">
		<?php echo $grid->sortTitle('機構編號', 'institute.id'); ?>
	</th>

	<!--Short Title-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('機構簡稱', 'institute.short_title'); ?>
	</th>

	<!--Delivery Weekday-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('外送日', 'institute.title'); ?>
	</th>

	<!--Color for Weekday-->
	<th width="5%" class="center">
		<?php echo $grid->sortTitle('註記顏色', 'institute.color_hex'); ?>
	</th>

	<!--Sender Name-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('外送藥師', 'institute.sender_name'); ?>
	</th>

	<!--Tel-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('電話', 'institute.tel'); ?>
	</th>

	<!--City-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('縣市', 'institute.city'); ?>
	</th>

	<!--Area-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('區域', 'institute.area'); ?>
	</th>

	<!--Address-->
	<th width="5%" class="left">
		<?php echo $grid->sortTitle('地址', 'institute.address'); ?>
	</th>

	<!--Link To Elder List-->
	<th width="5%" class="nowrap center">
		<?php echo JText::_('住民清單')?>
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

		<!--Edit button-->
		<td class="center">
			<?php echo \Schedule\Helper\UiHelper::editButton('institute', $item->id); ?>
		</td>

		<!--ID-->
		<td class="center">
			<?php echo $item->id;?>
		</td>

		<!--Short Title-->
		<td class="left">
			<a href="<?php echo JRoute::_('?option=com_schedule&view=institute&layout=edit&id=' . $item->id);?>"><?php echo $item->short_title;?></a>
		</td>

		<!--Delivery Weekday-->
		<td class="left">
			<?php echo JText::_($item->delivery_weekday);?>
		</td>

		<!--Color for Weekday-->
		<td class="center">
			<?php echo \Schedule\Helper\ColorHelper::getColorBlock($item->color_hex, 25); ?>
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
			<?php echo $item->area_title;?>
		</td>

		<!--Address-->
		<td class="left">
			<?php echo $item->address;?>
		</td>

		<!--Link To Elder List-->
		<td class="center">
			<a href="<?php echo JRoute::_('?option=com_schedule&view=customers&filter[customer.institute_id]=' . $item->id);?>" target="_blank">
				住民清單
				<i class="glyphicon glyphicon-share-alt"></i>
			</a>
		</td>

	</tr>
<?php endforeach; ?>
</tbody>
</table>
