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
<table id="customerList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!--EDIT-->
	<th width="5%" class="nowrap center">
		編輯
	</th>

	<!--CUSTOMER_ID-->
	<th width="5%" class="nowrap center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_ID', 'customer.id'); ?>
	</th>

	<!--CUSTOMER_TYPE-->
	<th width="5%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_TYPE', 'customer.type'); ?>
	</th>

	<!--CUSTOMER_NAME-->
	<th width="7%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_NAME', 'customer.name'); ?>
	</th>

	<!--CUSTOMER_ID_NUMBER-->
	<th width="5%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_ID_NUMBER', 'customer.id_number'); ?>
	</th>

	<!--CUSTOMER_CUSTOMER_AGE-->
	<th width="5%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_AGE', 'customer.age'); ?>
	</th>

	<!--CUSTOMER_CUSTOMER_MEMBER_MAPS-->
	<th width="10%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_MEMBER_MAPS', ''); ?>
	</th>

	<!--CITY TITLE-->
	<th width="5%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_CITY_TITLE', 'customer.city_title'); ?>
	</th>

	<!--AREA TITLE-->
	<th width="5%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_AREA_TITLE', 'customer.area_title'); ?>
	</th>

	<!--STATE-->
	<th width="5%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_STATE', 'customer.state'); ?>
	</th>

	<!--SCHEDULE-->
	<th width="5%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_SCHEDULE_RECORD', ''); ?>
	</th>

	<!--RESERVE-->
	<th width="4%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_CUSTOMER_RESERVE', ''); ?>
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
	<tr class="customer-row" <?php echo $item->catid; ?>>

		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>

		<!--EDIT-->
		<td class="center">
			<?php echo \Schedule\Helper\UiHelper::editButton('customer', $item->id); ?>
		</td>

		<!--CUSTOMER_ID-->
		<td class="center">
			<?php echo $this->escape($item->id); ?>
		</td>

		<!--CUSTOMER_TYPE-->
		<td class="center">
			<?php
			if($item->type == 'individual')
			{
				echo JTEXT::_('COM_CUSTOMERS_INDIVIDUAL');
			} else {
				echo JTEXT::_('COM_CUSTOMERS_RESIDENT');
			}
			?>
		</td>

		<!--CUSTOMER_NAME-->
		<td class="center">
			<?php echo $this->escape($item->name); ?>
		</td>


		<!--CUSTOMER_ID_NUMBER-->
		<td class="center">
			<?php echo $this->escape($item->id_number); ?>
		</td>

		<!--CUSTOMER_AGE-->
		<td class="center">
			<?php echo $this->escape($item->age); ?>
		</td>

		<!--CUSTOMER_MEMBER_MAPS-->
		<td class="center">
			<?php

			$attr = array('target' => '_blank');

			if ('individual' === $item->type)
			{
				$url  = 'index.php?option=com_schedule&task=member.edit.edit&id=' . $item->member_id;
				$text = '<span class="glyphicon glyphicon-user"></span> ' .
					$item->member_name .
					' <span class="glyphicon glyphicon-share-alt"></span>';

				echo \JHtml::link($url, $text, $attr);
			}

			if ('resident' === $item->type)
			{
				$url  = 'index.php?option=com_schedule&task=institute.edit.edit&id=' . $item->institute_id;
				$text = '<span class="glyphicon glyphicon-home"></span> ' .
					$item->institute_short_title .
					' <span class="glyphicon glyphicon-share-alt"></span>';

				echo \JHtml::link($url, $text, $attr);
			}
			?>
		</td>

		<!--CITY_TITLE-->
		<td class="center">
			<?php echo $this->escape($item->city_title); ?>
		</td>

		<!--AREA_TITLE-->
		<td class="center">
			<?php echo $this->escape($item->area_title); ?>
		</td>

		<!--STATE-->
		<td class="center">
			<?php echo $this->escape($item->state ? JTEXT::_('COM_CUSTOMERS_IN_SERVICE') : JTEXT::_('COM_CUSTOMERS_CLOSE_CASE')); ?>
		</td>

		<!--SCHEDULE_RECORD-->
		<td class="center">
			<a href="<?php echo JRoute::_('index.php?option=com_schedule&view=schedules');?>"><?php echo JTEXT::_('COM_SCHEDULE_RECORD_LINK');?></a>
		</td>

		<!--RESERVE-->
		<td class="center">
			<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_schedule&view=rxindividual&layout=edit');?>">
				<span class="glyphicon glyphicon-plus"></span>
			</a>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
