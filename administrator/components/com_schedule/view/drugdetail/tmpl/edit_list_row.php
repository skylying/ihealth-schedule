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

<!--style for schedules being cancelled-->
<style>
	 .status-mark
	 {
		 padding: 3px;
		 color: #ffffff;
		 border-radius: 5px;
	 }
	.cancel_reject
	{
		background: #95a5a6;
	}
	.cancel_only
	{
		background: #B766AD;
	}
	.pause
	{
		background: #f5ab35;
	}
	.emergency
	{
		background: #e74c3c;
	}
	.delivered
	{
		background: #16c02d;
	}
</style>

<tr>
	<td class="text-center">
		<!-- 排程編號 -->
		<div class="row"><?php echo $schedule->id; ?></div>
		<div class="row">
			<span class="status-mark <?php echo $schedule->status; ?>">
				<?php
				switch ($schedule->status)
				{
					case 'cancel_reject':
						echo '取退';
						break;

					case 'cancel_only':
						echo '取不';
						break;

					case 'pause':
						echo '緩';
						break;

					case 'emergency':
						echo '急';
						break;

					case 'delivered':
						echo '送';
						break;
				}
				?>
			</span>
		</div>
	</td>
	<td class="text-center">
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
			echo "<td class='alert alert-info'>" . $schedule->institute_title . $floor . "</td>";
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
			echo "<td colspan='2' class='center'>--</td>";
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
