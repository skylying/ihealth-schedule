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
$urlPrefix = JRoute::_('index.php?option=com_schedule&view=schedules&filter[schedule.member_id]=', false);
?>

<div>
<?php foreach ($notifies as $notify): ?>
	<div>
		<strong><?php echo $notify->member_name; ?></strong>
		<?php echo JText::_('COM_SCHEDULE_SCHEDULES_NOTIFY_' . $notify->notify); ?>

		<a href="<?php echo $urlPrefix . $notify->member_id; ?>" target="_blank" style="margin-left:0.5em;">
			<small>[ 詳細 ]</small>
		</a>

		<a href="#" class="text-muted" style="margin-left:0.5em;">
			<small>[ 略過 ]</small>
		</a>
	</div>
<?php endforeach; ?>
</div>
