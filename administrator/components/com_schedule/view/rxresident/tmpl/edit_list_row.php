<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Helper\Form\FieldHelper;

/**
 * Prepare data for this template.
 *
 * @var $data Windwalker\Data\Data
 * @var $form JForm
 */
$form  = $data->form;
$group = $data->group;
$id    = FieldHelper::resetGroup($form->getField('id'), $group);

$idPrefix   = preg_replace('/' . $id->fieldname . '$/', '', $id->id);
$namePrefix = preg_replace('/\[' . $id->fieldname . '\]$/', '', $id->name);

$parts = explode('_', trim($idPrefix, '_ '));
array_pop($parts);
$idReplace = implode('_', $parts) . '_{{hash}}_';

$parts = explode('][', $namePrefix);
array_pop($parts);
$nameReplace = implode('][', $parts) . '][{{hash}}]';
?>
<tr data-id-prefix="<?php echo $idPrefix; ?>"
	data-name-prefix="<?php echo $namePrefix; ?>"
	data-id-replace="<?php echo $idReplace; ?>"
	data-name-replace="<?php echo $nameReplace; ?>"
	>
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
			<button type="button" class="btn button-copy-row">
				<span class="glyphicon glyphicon-file"></span>
			</button>
			<button type="button" class="btn button-delete-row">
				<span class="glyphicon glyphicon-remove"></span>
			</button>
		</div>
	</td>
</tr>
