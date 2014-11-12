<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * @var Windwalker\Data\Data $displayData
 */
$notifies = $displayData->notifies;
$urlPrefix = JRoute::_('index.php?option=com_schedule&view=schedules&filter[schedule.customer_id]=', false);
?>

<div class="alert alert-warning">
<div>
	目前有 <span class="text-primary"><?php echo count($notifies); ?></span> 位客戶的排程需要被合併
	<a href="#" onclick="jQuery('#notifies').show();" style="margin-left:0.5em;">[ 顯示 ]</a>
	<a href="#" onclick="jQuery('#notifies').hide();" style="margin-left:0.5em;">[ 隱藏 ]</a>
</div>
<div id="notifies" style="display:none;padding-top:1em;">
<?php foreach ($notifies as $notify): ?>
	<div>
		客戶
		<strong><?php echo $notify->customer_name; ?></strong>
		<?php echo JText::_('COM_SCHEDULE_SCHEDULES_NOTIFY_' . $notify->notify); ?>

		<a href="<?php echo $urlPrefix . $notify->customer_id; ?>" target="_blank" style="margin-left:0.5em;">
			<small>[ 詳細 ]</small>
		</a>

		<a href="#" class="skip-notify-button" style="margin-left:0.5em;"
			data-schedule-cid="<?php echo $notify->id; ?>">
			<small>[ 略過 ]</small>
		</a>

		<?php
		if (JDEBUG)
		{
			echo '<small style="margin-left:0.5em;">排程編號: [' . $notify->id . ']</small>';
		}
		?>
	</div>
<?php endforeach; ?>
</div>
</div>

<script type="text/javascript">
	// 處理略過按鈕的 onclick 事件
	jQuery('#notifies').on('click', '.skip-notify-button', function()
	{
		jQuery('input[name=notify_schedule_cid]').val(jQuery(this).data('schedule-cid'));
		Joomla.submitbutton('schedules.skip.notify');
	});
</script>
