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
$status = strtolower(trim($data->item->status));
$options = array(
	'scheduled',
	'emergency',
	'cancel_reject',
	'cancel_only',
	'pause',
	// 'deleted',	// Not display in dropdown list, for document only.
);
?>

<div class="btn-group">
	<button type="button"
		class="btn btn-default btn-sm dropdown-toggle"
		data-toggle="dropdown"
		style="background:<?php echo JText::_($prefix . $status . '_COLOR'); ?>;">
		<i class="glyphicon glyphicon-<?php echo JText::_($prefix . $status . '_ICON'); ?>"></i>
		<?php echo JText::_($prefix . $status); ?>
		<span class="caret"></span>
	</button>

	<ul class="dropdown-menu">
		<?php foreach ($options as $option): ?>
			<?php
			if ($status === $option)
			{
				continue;
			}
			?>
		<li style="background:<?php echo JText::_($prefix . $option . '_COLOR'); ?>;"
			data-status="<?php echo strtolower($option); ?>">
			<a href="#" class="update-status-button">
				<i class="glyphicon glyphicon-<?php echo JText::_($prefix . $option . '_ICON'); ?>"></i>
				<?php echo JText::_($prefix . $option); ?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
