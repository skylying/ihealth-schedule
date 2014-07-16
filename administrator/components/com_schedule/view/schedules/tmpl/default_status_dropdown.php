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
<div class="btn-group status-dropdown-menu">
	<button type="button"
		id="btn-status-dp-<?php echo $index; ?>"
		class="btn btn-default btn-sm dropdown-toggle <?php echo $classTooltip; ?>"
		data-toggle="dropdown"
		data-default-cancel="<?php echo $data->item->cancel; ?>"
		data-default-cancel-note="<?php echo $data->item->cancel_note; ?>"
		<?php echo $showTooltip; ?>
		style="background:<?php echo JText::_($prefix . $status . '_COLOR'); ?>;">
		<span class="glyphicon glyphicon-<?php echo JText::_($prefix . $status . '_ICON'); ?>"></span>
		<?php echo JText::_($prefix . $status); ?>
		<span class="caret"></span>
	</button>

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
			<a href="#">
				<span class="glyphicon glyphicon-<?php echo JText::_($prefix . $option . '_ICON'); ?>"></span>
				<?php echo JText::_($prefix . $option); ?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
