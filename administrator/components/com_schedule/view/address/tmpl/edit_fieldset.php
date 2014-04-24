<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$fieldset = $data->fieldset;
?>
<fieldset id="address-edit-fieldset-<?php echo $fieldset->name ?>" class="<?php echo $data->class ?>">
	<?php foreach ($data->form->getFieldset($fieldset->name) as $field): ?>
		<div id="control_<?php echo $field->id; ?>">
			<?php echo $field->getControlGroup() . "\n\n"; ?>
		</div>
	<?php endforeach;?>
</fieldset>
