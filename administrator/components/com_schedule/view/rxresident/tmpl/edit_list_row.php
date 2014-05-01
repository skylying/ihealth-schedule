<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Helper\Form\FieldHelper;
use Schedule\Helper\Form\MultiRowHelper;

/**
 * Prepare data for this template.
 *
 * @var $data Windwalker\Data\Data
 * @var $form JForm
 */
$form  = $data->form;
$group = $data->group;
$id    = FieldHelper::resetGroup($form->getField('id'), $group);
?>
<tr <?php echo MultiRowHelper::getRowDataAttributes($id->id, $id->fieldname, $id->name); ?>>
	<td>
		<?php echo $id->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('customer_id'), $group)->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('id_number'), $group)->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('birth_date'), $group)->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('see_dr_date'), $group)->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('period'), $group)->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('times'), $group)->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('deliver_nths'), $group)->input; ?>
	</td>
	<td>
		<?php
		$field1st = FieldHelper::resetGroup($form->getField('empty_date_1st'), $group);
		$field2nd = FieldHelper::resetGroup($form->getField('empty_date_2nd'), $group);
		?>
		(1) <?php echo substr($field1st->value, 5); ?>
		<br />
		(2) <?php echo substr($field2nd->value, 5); ?>

		<?php echo $field1st->input; ?>
		<?php echo $field2nd->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('method'), $group)->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('note'), $group)->input; ?>
	</td>
	<td>
		<div class="btn-group">
			<button type="button" class="btn btn-default button-copy-row">
				<span class="glyphicon glyphicon-file"></span>
			</button>
			<button type="button" class="btn btn-default button-delete-row">
				<span class="glyphicon glyphicon-remove"></span>
			</button>
		</div>
	</td>
</tr>
