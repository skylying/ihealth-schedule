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

<div>
<?php foreach ($notifies as $notify): ?>
	<div>
		客戶
		<strong><?php echo $notify->customer_name; ?></strong>
		<?php echo JText::_('COM_SCHEDULE_SCHEDULES_NOTIFY_' . $notify->notify); ?>

		<a href="<?php echo $urlPrefix . $notify->customer_id; ?>" target="_blank" style="margin-left:0.5em;">
			<small>[ 詳細 ]</small>
		</a>

		<a href="#" class="skip-notify-button" style="margin-left:0.5em;"
			data-schedule-cid="<?php echo $notify->id; ?>"
			onclick="
				jQuery('input[name=notify_schedule_cid]').val(jQuery(this).data('schedule-cid'));
				Joomla.submitbutton('schedules.skip.notify');
			">
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
