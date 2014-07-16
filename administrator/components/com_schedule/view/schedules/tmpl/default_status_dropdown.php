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
	'delivered'     => ['updateMethod' => 'submit'],
	// 'deleted',	// Not display in dropdown list, for document only.
];
$optionForNormal = [
	'scheduled' => ['updateMethod' => 'submit'],
	'canceled'  => ['updateMethod' => 'modal-cancel'],
	'pause'     => ['updateMethod' => 'modal-pause'],
	'delivered' => ['updateMethod' => 'submit'],
];

$options = ($type == 'individual' || $type == 'resident') ? $optionForDelivery : $optionForNormal;
?>

<div class="btn-group status-dropdown-menu">
	<button type="button"
		class="btn btn-default btn-sm dropdown-toggle"
		data-toggle="dropdown"
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
