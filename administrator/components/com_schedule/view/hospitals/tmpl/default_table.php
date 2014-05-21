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
<table id="hospitalList" class="table table-striped adminlist">

	<!-- TABLE HEADER -->
	<thead>
	<tr>
		<!--CHECKBOX-->
		<th width="1%" class="center">
			<?php echo JHtml::_('grid.checkAll'); ?>
		</th>

		<!-- EDIT BUTTON-->
		<th width="5%" class="nowrap center">
			編輯
		</th>

		<!--HOSPITAL ID-->
		<th width="5%" class="nowrap center">
			<?php echo $grid->sortTitle('醫院編號', 'hospitals.id'); ?>
		</th>

		<!--HOSPITA TITLE-->
		<th width="49%" class="left">
			<?php echo $grid->sortTitle('醫院名稱', 'hospitals.title'); ?>
		</th>

		<!--CITY_TITLE-->
		<th width="15%" class="center">
			<?php echo $grid->sortTitle('縣市名稱', 'city.title'); ?>
		</th>

		<!--AREA_TITLE-->
		<th width="15%" class="center">
			<?php echo $grid->sortTitle('區域名稱', 'area.title'); ?>
		</th>

		<!--HAS_HI_CODE-->
		<th width="15%" class="center">
			<?php echo $grid->sortTitle('藥品健保碼', 'hospitals.hicode'); ?>
		</th>
	</tr>
	</thead>

	<!--PAGINATION-->
	<tfoot>
	<tr>
		<td colspan="6">
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
		<tr class="hospital-row">
			<!--CHECKBOX-->
			<td class="center">
				<?php echo JHtml::_('grid.id', $i, $item->id); ?>
			</td>

			<!-- EDIT BUTTON-->
			<td class="center">
				<?php echo \Schedule\Helper\UiHelper::editButton('hospital', $item->id); ?>
			</td>

			<!--ID-->
			<td class="center">
				<?php echo (int) $item->id; ?>
			</td>

			<!--HOSPITAL TITLE-->
			<td class="nowrap quick-edit-wrap">
				<div class="item-title left">
					<?php
					$query = array(
						'option' => 'com_schedule',
						'view'   => 'hospital',
						'layout' => 'edit',
						'id'     => $item->id
					);
					?>
						<?php echo $this->escape($item->title); ?>
				</div>
			</td>

			<!--CITY TITLE-->
			<td class="center">
				<?php echo $this->escape($item->city_title); ?>
			</td>

			<!--AREA TITLE-->
			<td class="center">
				<?php echo $this->escape($item->area_title); ?>
			</td>

			<!--HAS HI CODE-->
			<td class="center">
				<?php echo $item->has_hicode ? '<span class="label label-success"> 有 </span>' : '<span class="label label-danger"> 沒有 </span>' ; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

