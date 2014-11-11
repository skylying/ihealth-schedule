<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Helper\Mapping\MemberCustomerHelper;
use Schedule\Helper\Form\FieldHelper;
use Windwalker\Joomla\DataMapper\DataMapper;

/**
 * Prepare data for this template.
 *
 * @var $data Windwalker\Data\Data
 * @var $form JForm
 */
$form     = $data->form;
$schedule = $data->schedule;

$app = JFactory::getApplication();
$sortedList = $app->getUserState('drugdetail.sorted.list');

$noidValue = (new JRegistry($schedule->params))->get('noid', false);

$noid   = FieldHelper::resetGroup($form->getField('noid', null, $noidValue ? 1 : 0), "schedules.{$schedule->id}");
$sorted = FieldHelper::resetGroup($form->getField('sorted', null, $schedule->sorted), "schedules.{$schedule->id}");
$ice    = FieldHelper::resetGroup($form->getField('ice', null, $schedule->ice), "schedules.{$schedule->id}");
$price  = FieldHelper::resetGroup($form->getField('price', null, (int) $schedule->price), "schedules.{$schedule->id}");
$modified_by = FieldHelper::resetGroup($form->getField('modified_by', null, 'hello'), "schedules.{$schedule->id}");

$floor = $schedule->floor ? $schedule->floor : '';

// Used for compare if sorted field is changed
if (!isset($sortedList) || empty($sortedList))
{
	$app->setUserState('drugdetail.sorted.list', [$schedule->id => $schedule->sorted]);
}
else
{
	$sortedList[$schedule->id] = $schedule->sorted;

	$app->setUserState('drugdetail.sorted.list', $sortedList);
}
?>

<tr data-id="<?php echo $schedule->id; ?>">
	<td class="text-center">
		<!-- 排程編號 -->
		<div class="row">
			<a href="<?php echo JRoute::_('index.php?option=com_schedule&view=schedules&filter[schedule.id]=' . $schedule->id)?>" target="_blank">
				<?php echo $schedule->id; ?>
			</a>
		</div>
		<div class="row">
			<span class="status-mark <?php echo $schedule->status; ?>">
				<?php
				// 已外送 & 已排程不需要顯示
				if ($schedule->status !== 'scheduled' && $schedule->status !== 'delivered')
				{
					echo JText::_('COM_SCHEDULE_DRUGDETAIL_CANCEL_STATUS_' . strtoupper($schedule->status));
				}
				?>
			</span>
		</div>
	</td>
	<td class="text-center">
		<!-- 處方編號 -->
		<?php if ("resident" === $schedule->type): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_schedule&view=rxresident&layout=edit_list&id=' . $schedule->rx_id); ?>" target="_blank">
		<?php elseif ("individual" === $schedule->type): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_schedule&view=rxindividual&layout=edit&id=' . $schedule->rx_id); ?>" target="_blank">
		<?php else: ?>
		<a href="#">
		<?php endif; ?>
			<?php echo $schedule->rx_id; ?>
		</a>
	</td>
	<td>
		<!-- 處方建立時間 -->
		<?php echo substr($schedule->created, 0, 10); ?>
	</td>
	<!-- 縣市/區域 -->
	<?php if ("resident" === $schedule->type): ?>
		<td colspan='2' class='center'>--</td>
	<?php elseif ("individual" === $schedule->type): ?>
		<td><?php echo $schedule->city_title; ?></td>
		<td><?php echo $schedule->area_title; ?></td>
	<?php endif; ?>
	<!-- 所屬機構/會員 -->
	<?php if ("resident" === $schedule->type): ?>
		<td class='alert alert-info'><?php echo $schedule->institute_title . $floor; ?></td>
	<?php elseif ("individual" === $schedule->type): ?>
		<td class='alert alert-warning'><?php echo $schedule->member_name; ?></td>
	<?php endif; ?>
	<td>
		<!-- 吃完藥日 -->
		<?php echo $schedule->drug_empty_date; ?>
	</td>
	<td>
		<!-- 客戶 -->
		<?php echo $schedule->customer_name; ?>
	</td>
	<td class="big-checkbox-td text-center">
		<!-- 分藥完成 form -->
		<?php echo $sorted->getControlGroup(); ?>
	</td>
	<td class="big-checkbox-td text-center">
		<!-- 缺 ID -->
		<?php echo $noid->getControlGroup(); ?>
	</td>
	<td class="big-checkbox-td text-center">
		<!-- 冰品 -->
		<?php echo $ice->getControlGroup(); ?>
	</td>
	<td>
		<!-- 自費金額 -->
		<?php echo $price->input; ?>
	</td>
	<td class="text-center">
		<?php
		if ($schedule->type == 'individual')
		{
			$trueConfig = ['class' => 'btn btn-primary', 'content' => 'Y'];
			$falseConfig = ['class' => 'btn btn-danger', 'content' => 'N'];

			$config = $schedule->need_split ? $trueConfig : $falseConfig;

			echo '<span class="' . $config['class'] . '">' . $config['content'] . '</span>';
		}
		else
		{
			echo '--';
		}
		?>
	</td>
	<td>
		<?php
		if (!empty($schedule->modified_by))
		{
			echo JUser::getInstance($schedule->modified_by)->name;
		}
		?>
	</td>
</tr>
