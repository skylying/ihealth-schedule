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
<tr>
	<td>
		<!-- 排程編號 -->
		<?php echo $schedule->id; ?>
	</td>
	<td>
		<!-- 處方編號 -->
		<?php echo $schedule->rx_id; ?>
	</td>
	<td>
		<!-- 處方建立時間 -->
		<?php echo substr($schedule->created, 0, 10); ?>
	</td>
	<td>
		<!-- 吃完藥日 -->
		<?php echo $schedule->drug_empty_date; ?>
	</td>
	<!-- 所屬機構/會員 -->
	<?php
	switch ($schedule->type)
	{
		case "resident" :
			echo "<td class='alert alert-info'>" . $schedule->institute_title . "</td>";
		break;

		case "individual" :
			echo "<td class='alert alert-warning'>" . $schedule->member_name . "</td>";
		break;
	}
	?>

	<?php
	switch ($schedule->type)
	{
		case "resident" :
			echo "<td colspan='2' class='center'>-</td>";
			break;

		case "individual" :
			echo "<td>" . $schedule->city_title . "</td>";
			echo "<td>" . $schedule->area_title . "</td>";
			break;
	}
	?>
	<td>
		<!-- 客戶 -->
		<?php echo $schedule->customer_name; ?>
	</td>
	<td class="big-checkbox-td text-center">
		<!-- 缺 ID -->
		<?php echo $noid->getControlGroup(); ?>
	</td>
	<td class="big-checkbox-td text-center">
		<!-- 分藥完成 form -->
		<?php echo $sorted->getControlGroup(); ?>
	</td>
	<td class="big-checkbox-td text-center">
		<!-- 冰品 -->
		<?php echo $ice->getControlGroup(); ?>
	</td>
	<td>
		<!-- 自費金額 -->
		<?php echo $price->input; ?>
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
