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
<table id="memberList" class="table table-striped adminlist">

	<!-- TABLE HEADER -->
	<thead>
	<tr>
		<!--CHECKBOX-->
		<th width="1%" class="center">
			<?php echo JHtml::_('grid.checkAll'); ?>
		</th>

		<!-- EDIT -->
		<th width="3%" class="center nowrap">
			編輯
		</th>

		<!--ID-->
		<th width="10%" class="center">
			<?php echo $grid->sortTitle('會員編號', 'member.id'); ?>
		</th>

		<!--NAME-->
		<th width="10%" class="left">
			<?php echo $grid->sortTitle('會員姓名', 'member.name'); ?>
		</th>

		<!--EMAIL-->
		<th width="25%" class="left">
			<?php echo $grid->sortTitle('會員信箱', 'member.email'); ?>
		</th>

		<!--Customer Amount-->
		<th width="10%" class="center">
			<?php echo $grid->sortTitle('散客數', 'member.email'); ?>
		</th>

		<!--Relative Customers-->
		<th width="35%" class="left">
			<?php echo $grid->sortTitle('對應散客', 'member.email'); ?>
		</th>
	</tr>
	</thead>

	<!--PAGINATION-->
	<tfoot>
	<tr>
		<td colspan="11%">
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
		<tr class="member-row">
			<!--ID-->
			<td class="center">
				<?php echo JHtml::_('grid.id', $i, $item->id); ?>
			</td>

			<!-- EDIT BUTTON -->
			<td class="center">
				<?php echo \Schedule\Helper\UiHelper::editButton('member', $item->id); ?>
			</td>

			<!--ID-->
			<td class="center">
				<?php echo $item->id; ?>
			</td>

			<!--NAME-->
			<td class="nowrap quick-edit-wrap">
				<div class="left">
					<?php echo $this->escape($item->name); ?>
				</div>
			</td>

			<!--EMAIL-->
			<td class="left">
				<?php echo $this->escape($item->email); ?>
			</td>

			<?php
			$customerNames = explode(',', $item->customers_name);
			$customerIds= explode(',', $item->customers_id);
			?>
			<!--Customer Amount-->
			<td class="center">
				<?php echo empty($item->customers_name) ? '0' : count(explode(',', $item->customers_name));?>
			</td>

			<!--Relative Customers-->
			<td class="left">
				<?php
				foreach ($customerNames as $i => $customer)
				{
					if(!empty($customerIds[$i]))
					{
						echo Schedule\Helper\UiHelper::foreignLink('customer', $customer, $customerIds[$i], '', array('target' => '_blank'));
						if((count($customerNames)-1) > $i) echo ', ';
					}
				}
				?>
			</td>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>
