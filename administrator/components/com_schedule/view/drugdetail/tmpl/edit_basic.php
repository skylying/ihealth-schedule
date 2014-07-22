<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$container = $this->getContainer();
$asset = $container->get('helper.asset');
$form = $data->form;

JHtmlJquery::framework(true);

$asset->addJS('drugdetail/institute-extra.js');

// Prepare user for javascript
$user = JFactory::getUser();
?>

<script>
	jQuery(document).ready(function()
	{
		var btnClass = 'add-institute-extra',
			deleteBtnClass = 'row-remove-button',
			rowIdPrefix = 'row-institute-',
			userId = '<?php echo $user->id; ?>';

		window.InstituteExtraObject = new InstituteExtra(btnClass, deleteBtnClass, rowIdPrefix, userId);
	});
</script>

<h1 class="text-center"><?php echo $data->date; ?> 打包表</h1>

<?php foreach ($data->items as $sender):?>

<span class="btn btn-info" style="font-size: 20px;">
	<span class="icon-signup"></span>&nbsp;&nbsp;<?php echo $sender['name']; ?>
</span>
	<hr />
<table id="drug-details" class="table table-bordered">
	<thead>
	<tr>
		<th>
			排程編號
		</th>
		<th>
			處方箋編號
		</th>
		<th>
			新增處方箋日
		</th>
		<th>
			吃完藥日
		</th>
		<th>
			所屬機構/會員
		</th>
		<th>
			縣市
		</th>
		<th>
			區域
		</th>
		<th>
			客戶
		</th>
		<th>
			缺ID
		</th>
		<th width="7%">
			完成分藥
		</th>
		<th width="7%">
			冰品
		</th>
		<th>
			自費金額
		</th>
		<th>
			最後分藥人
		</th>
	</tr>
	</thead>
	<tbody>

	<!--Start of institute schedules-->

	<?php

	// 總份數
	$total = 0;

	// Count individual schedules
	$individualSchedulesCount = count($sender['individuals']);

	$total += $individualSchedulesCount;

	foreach ($sender['institutes'] as $instituteId => $institute):

		// Count resident schedules
		$residentSchedulesCount = count($institute['schedules']);

		// Add up resident schedules
		$total += $residentSchedulesCount;

		/* 先跑同機構下的所有住民排程 */
		foreach ($institute['schedules'] as $schedule)
		{
			echo $this->loadTemplate('list_row', array('schedule' => $schedule));
		}

		// Prepare task id and drugDetails
		$taskId = $sender['task_id'];
		$drugDetails = empty($data->extras[$taskId][$instituteId]) ? array() : $data->extras[$taskId][$instituteId];

		/* 接著跑所有此機構下的加購藥品資訊 (如果不為空)*/
		if (!empty($drugDetails))
		{
			foreach ($drugDetails as $detail)
			{
				echo $this->loadTemplate('extra_list_row', array(
						'id'      => '',
						'class'   => '',
						'extra'   => $detail,
						'task_id' => $sender['task_id'],
						'group'   => "institutes.{$instituteId}.{$detail->id}",
						'isJs'    => false)
				);
			}
		}

		/* Empty hidden template */
		echo $this->loadTemplate('extra_list_row', array(
				'id'      => "row-institute-{$instituteId}",
				'class'   => 'hide',
				'extra'   => null,
				'task_id' => $sender['task_id'],
				'group'   => "institutes.{$instituteId}.0hash0",
				'isJs'    => true)
		);
		?>

	<tr>
		<td colspan="12" class="text-right">機構小計份數： <?php echo $residentSchedulesCount; ?> 份</td>
		<td>
			<button class="add-institute-extra btn btn-success" data-institute-id="<?php echo $instituteId; ?>" type="button">
				<span class="icon-new"></span>
			</button>
		</td>
	</tr>

	<?php endforeach; ?>
	<!--End of institute schedules-->

	<!--Start of individual schedules-->
	<?php
	if (!empty($sender['individuals']))
	{
		foreach ($sender['individuals'] as $schedule)
		{
			echo $this->loadTemplate('list_row', array('schedule' => $schedule));
		}

		$summaryRow = <<<HTML
<tr>
		<td colspan="11" class="text-right">散客小計份數： {$individualSchedulesCount} 份</td>
		<td></td>
	</tr>
HTML;

		echo $summaryRow;
	}
	?>
	<!--End of individual schedules-->

	<tr>
		<td colspan="12" class="text-right">
			<h3>總計份數： <?php echo $total; ?> 份</h3>
		</td>
		<td></td>
	</tr>
	</tbody>
</table>
<?php endforeach; ?>
