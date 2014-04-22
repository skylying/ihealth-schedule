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
<table id="addressList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--ID-->
	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('JGRID_HEADING_ID', 'address.id'); ?>
	</th>

	<!--CITY-->
	<th width="10%" class="center">
		<?php echo $grid->sortTitle('縣市', 'address.city_title'); ?>
	</th>

	<!--AREA-->
	<th width="10%" class="center">
		<?php echo $grid->sortTitle('區域', 'address.area_title'); ?>
	</th>

	<!--ADDRESS-->
	<th class="center">
		<?php echo $grid->sortTitle('路名', 'address.address'); ?>
	</th>

	<!--CUSTOMER_ID-->
	<th class="center">
		<?php echo $grid->sortTitle('對應客戶', 'address.customer_id'); ?>
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
	<tr class="address-row" sortable-group-id="<?php echo $item->catid; ?>">
		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->address_id); ?>
		</td>

		<!--ID-->
		<td class="center">
			<a href="<?php echo JRoute::_('index.php?option=com_schedule&view=address&layout=edit&id=' . $item->id); ?>"><?php echo $item->id; ?></a>
		</td>

		<!--CITY-->
		<td class="center">
			<?php echo $item->city_title; ?>
		</td>

		<!--AREA-->
		<td class="center">
			<?php echo $item->area_title; ?>
		</td>

		<!--ADDRESS-->
		<td class="center">
			<?php echo $item->address; ?>
		</td>

		<!--ADDRESS-->
		<td class="center"><a
				href="<?php
				$proxy = array(
					'option' => 'com_schedule',
					'view'   => 'member',
					'layout' => 'edit',
					'id'	 => $item->customer_id
				);
				echo JRoute::_('index.php?' . http_build_query($proxy)); ?>"
				target="_blank">
				<?php echo $item->customer_id; ?>&nbsp;<span class="glyphicon glyphicon-share-alt"></span>
			</a>
		</td>

	</tr>
<?php endforeach; ?>
</tbody>
</table>
