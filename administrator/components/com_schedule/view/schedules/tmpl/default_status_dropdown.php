<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Prepare data for this template.
 *
 * @var $data Windwalker\Data\Data
 */
$prefix = 'COM_SCHEDULE_SCHEDULE_FIELD_STATUS_';
$index = $data->index;
$status = strtolower(trim($data->item->status));
$type = $data->item->type;
$optionForDelivery = [
	'scheduled'     => ['updateMethod' => 'submit'],
	'emergency'     => ['updateMethod' => 'submit'],
	'cancel_reject' => ['updateMethod' => 'modal-cancel'],
	'cancel_only'   => ['updateMethod' => 'modal-cancel'],
	'pause'         => ['updateMethod' => 'modal-pause'],
	// 'deleted',	// Not display in dropdown list, for document only.
];
$optionForNormal = [
	'scheduled'     => ['updateMethod' => 'submit'],
	'canceled'        => ['updateMethod' => 'modal-cancel'],
	'pause'         => ['updateMethod' => 'modal-pause'],
];

$options = ($type == 'individual' || $type == 'resident') ? $optionForDelivery : $optionForNormal;

$classTooltip = '';
$showTooltip = '';

if ($status != 'scheduled' && $status != 'emergency')
{
	$classTooltip = 'hasTooltip';
	$cancelNote = $data->item->cancel_note;

	$cancelReason = $data->item->cancel;

	$reason = ($data->item->cancel) ? JText::_($prefix . 'REASON_' . $cancelReason) : '';

	$cancelOrPause = ($status == 'pause') ? '暫緩原因' : '取消原因';

	$showTooltip = 'title="<strong>' . $cancelOrPause . ':</strong>' . $reason . '<br /><strong>備註:</strong>' . $cancelNote . '"';
}
?>
<script>
	function transferStatus(theIndex)
	{
		var cancelReason = document.getElementById('status-cancelReason-' + theIndex).value;
		var cancelNote = document.getElementById('status-cancelNote-' + theIndex).value;

		if (cancelReason == 'badservice') document.getElementById('status-cancel-reason-1').checked = true;
		if (cancelReason == 'changedrug') document.getElementById('status-cancel-reason-2').checked = true;
		if (cancelReason == 'passaway') document.getElementById('status-cancel-reason-3').checked = true;
		if (cancelReason == 'hospitalized') document.getElementById('pause-status-cancel-reason-1').checked = true;

		if(cancelReason == 'other')
		{
			document.getElementById('status-cancel-reason-4').checked = true;
			document.getElementById('pause-status-cancel-reason-2').checked = true;
		}

		document.getElementById('status-cancel-note').value = cancelNote;
		document.getElementById('pause-status-cancel-note').value = cancelNote;
	}
</script>
<div class="btn-group status-dropdown-menu">
	<button type="button"
		class="btn btn-default btn-sm dropdown-toggle <?php echo $classTooltip; ?>"
		data-toggle="dropdown"
		<?php echo $showTooltip; ?>
		style="background:<?php echo JText::_($prefix . $status . '_COLOR'); ?>;">
		<span class="glyphicon glyphicon-<?php echo JText::_($prefix . $status . '_ICON'); ?>"></span>
		<?php echo JText::_($prefix . $status); ?>
		<span class="caret"></span>
	</button>

	<input type="hidden" id="status-cancelReason-<?php echo $index; ?>" name="status-cancel-reason" value="<?php echo $data->item->cancel; ?>" />
	<input type="hidden" id="status-cancelNote-<?php echo $index; ?>" name="status-cancel-note" value="<?php echo $data->item->cancel_note; ?>" />

	<ul class="dropdown-menu">
		<?php foreach ($options as $option => $config): ?>
			<?php
			if ($status === $option)
			{
				continue;
			}

			$showOnclickStatus = 'onclick="transferStatus(' . $index . ');"';
			?>
		<li style="background:<?php echo JText::_($prefix . $option . '_COLOR'); ?>;"
			data-status="<?php echo $option; ?>"
			data-index="<?php echo $index; ?>"
			data-update-method="<?php echo $config['updateMethod']; ?>">
			<a href="#" <?php echo $showOnclickStatus; ?>>
				<span class="glyphicon glyphicon-<?php echo JText::_($prefix . $option . '_ICON'); ?>"></span>
				<?php echo JText::_($prefix . $option); ?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
