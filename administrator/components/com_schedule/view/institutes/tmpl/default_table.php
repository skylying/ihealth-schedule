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
	<!--SORT-->
	<th width="1%" class="nowrap center hidden-phone">
		<?php echo $grid->orderTitle(); ?>
	</th>

	<!--CHECKBOX-->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_SHORT_TITLE', 'institute.short_title'); ?>
	</th>

	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_DELIVERY_WEEKDAY', 'institute.title'); ?>
	</th>

	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_LAST_DELIVERY_WEEKDAY', 'category.title'); ?>
	</th>

	<th width="5%" class="left">
		<?php echo $grid->sortTitle('JGRID_HEADING_ACCESS', 'viewlevel.title'); ?>
	</th>

	<th width="5%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_MARK_COLOR', 'institute.created'); ?>
	</th>

	<th width="10%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_SENDER_NAME', 'user.name'); ?>
	</th>

	<th width="5%" class="center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_TEL', 'lang.title'); ?>
	</th>

	<th width="1%" class="left">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_CITY', 'institute.id'); ?>
	</th>

	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_AREA', 'institute.id'); ?>
	</th>

	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_CITY', 'institute.id'); ?>
	</th>

	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_ADDRESS', 'institute.id'); ?>
	</th>

	<th width="1%" class="nowrap center">
		<?php echo $grid->sortTitle('COM_SCHEDULE_INSTITUTE_SERIAL', 'institute.id'); ?>
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
		<!-- DRAG SORT -->
		<td class="order nowrap center hidden-phone">
			<?php echo $grid->dragSort(); ?>
		</td>

		<!--CHECKBOX-->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->institute_id); ?>
		</td>

		<td class="left">
			<?php echo $item->short_title;?>
		</td>

		<td class="left">
			<?php echo $item->delivery_weekday;?>
		</td>

		<td class="center">
			<?php echo $item->delivery_weekday;?>
		</td>

		<td class="center">
			<?php echo $this->escape($item->viewlevel_title); ?>
		</td>

		<!--CREATED-->
		<td class="center">
			<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
		</td>

		<!--USER-->
		<td class="center">
			<?php echo $this->escape($item->user_name); ?>
		</td>

		<!--LANGUAGE-->
		<td class="center">
			<?php
			if ($item->language == '*')
			{
				echo JText::alt('JALL', 'language');
			}
			else
			{
				echo $item->lang_title ? $this->escape($item->lang_title) : JText::_('JUNDEFINED');
			}
			?>
		</td>

		<!--ID-->
		<td class="center">
			<?php echo (int) $item->id; ?>
		</td>

	</tr>
<?php endforeach; ?>
</tbody>
</table>
