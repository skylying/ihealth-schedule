<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Data\Data;
use Windwalker\Helper\ArrayHelper;

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

$printForm = $data->printForm;


$doc = JFactory::getDocument();
$css = <<<CSS
ol
{
	list-style-type:none;
}

ol li
{
	float:left;
	margin: 0 10px;
	padding: 0 10px;
}

ol li label
{
	float:right;
	display:inline;
	margin: 0 2px;
	padding: 0 2px;
}
CSS;

$doc->addStyleDeclaration($css);
?>

<!-- LIST TABLE -->
<table id="scheduleList" class="table table-striped adminlist">

<!-- TABLE HEADER -->
<thead>
<tr>
	<!-- CHECKBOX -->
	<th width="1%" class="center">
		<?php echo JHtml::_('grid.checkAll'); ?>
	</th>

	<!-- EDIT -->
	<th width="3%" class="center nowrap">
		編輯
	</th>

	<!-- schedule.id -->
	<th width="5%" class="nowrap">
		<?php echo $grid->sortTitle('排程編號', 'schedule.id'); ?>
	</th>

	<!-- schedule.type -->
	<th width="5%" class="nowrap">
		<?php echo $grid->sortTitle('類別', 'schedule.type'); ?>
	</th>

	<!-- schedule.institute_id | schedule.customer_id -->
	<th>
		<?php echo $grid->sortTitle('所屬機構/所屬會員', 'schedule.type, schedule.institute_id, schedule.customer_id'); ?>
	</th>

	<!-- schedule.city -->
	<th class="center">
		<?php echo $grid->sortTitle('縣市', 'schedule.city'); ?>
	</th>

	<!-- schedule.area -->
	<th class="center">
		<?php echo $grid->sortTitle('區域', 'schedule.area'); ?>
	</th>

	<!-- schedule.customer_id -->
	<th class="center">
		<?php echo $grid->sortTitle('客戶', 'schedule.customer_id'); ?>
	</th>

	<!-- schedule.date -->
	<th class="center">
		<?php echo $grid->sortTitle('預計外送日', 'schedule.date'); ?>
	</th>

	<!-- route.sender_id -->
	<th class="center">
		<?php echo $grid->sortTitle('外送藥師', 'route.sender_id'); ?>
	</th>

	<!-- schedule.sorted -->
	<th class="center">
		<?php echo $grid->sortTitle('分藥', 'schedule.sorted'); ?>
	</th>

	<!-- schedule.status -->
	<th>
		<?php echo $grid->sortTitle('狀態', 'schedule.status'); ?>
	</th>
</tr>
</thead>

<!--PAGINATION-->
<tfoot>
<tr>
	<td colspan="12">
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
	<tr class="schedule-row">
		<!-- CHECKBOX -->
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>

		<!-- EDIT -->
		<td class="center">
			<a class="btn btn-mini btn-primary"
				href="<?php echo JRoute::_('index.php?option=com_schedule&task=schedule.edit.edit&id=' . $item->id); ?>">
				<span class="glyphicon glyphicon-edit"></span>
			</a>
		</td>

		<!-- id -->
		<td class="center">
			<?php echo $item->id; ?>
		</td>

		<!-- type -->
		<td>
			<?php
			$styles = array('individual' => 'btn-info', 'resident' => 'btn-warning');
			$buttonStyle = ArrayHelper::getValue($styles, $item->type, 'btn-inverse');
			?>
			<button type="button" style="padding: 3px 8px;" class="btn <?php echo $buttonStyle?>"><?php echo JText::_('COM_SCHEDULE_SCHEDULE_FIELD_TYPE_' . $item->type);?></button>
		</td>

		<!-- customer_name | institute_name -->
		<td>
			<?php echo Schedule\Helper\ScheduleHelper::getTargetLink($item); ?>
		</td>

		<!-- city_title -->
		<td class="center">
			<?php echo $item->city_title; ?>
		</td>

		<!-- area_title -->
		<td class="center">
			<?php echo $item->area_title; ?>
		</td>

		<!-- customer_name -->
		<td class="center">
			<?php
			if ($item->customer_id > 0)
			{
				echo Schedule\Helper\UiHelper::foreignLink('customer', $item->customer_name, $item->customer_id, '', array('target' => '_blank'));
			}
			?>
		</td>

		<!-- date -->
		<td class="center">
			<?php echo $item->date; ?>
		</td>

		<!-- route_sender_name -->
		<td class="center">
			<?php echo $item->sender_name; ?>
		</td>

		<!-- sorted -->
		<td class="center">
			<span class="glyphicon glyphicon-<?php echo ($item->sorted ? 'ok' : 'remove'); ?>"></span>
		</td>

		<!-- status -->
		<td>
			<?php
			if ($item->status)
			{
				echo $this->loadTemplate('status_dropdown', array('item' => $item));
			}
			?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>

<div class="hide">
	<div id="print">
			<div class="form-horizontal">
				<?php foreach ($printForm->getFieldset('schedules_print') as $field): ?>
				<div id="control_<?php echo $field->id; ?>">
					<?php echo $field->getControlGroup();?>
				</div>
				<?php endforeach;?>
			</div>

			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('schedules.report')">
				<span class="glyphicon glyphicon-print"></span>
					產生報表
			</button>

			<button type="button" onclick="window.parent.SqueezeBox.close();" class="btn btn-default">
				<span class="glyphicon glyphicon-remove"></span>
				取消
			</button>
	</div>
</div>
