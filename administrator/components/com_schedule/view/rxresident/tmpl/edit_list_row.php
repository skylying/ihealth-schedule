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
?>
<tr>
	<td>
		<?php
		$customerId = FieldHelper::resetGroup($form->getField('customer_id'), $group);

		if (!$data->isNew)
		{
			$customerId->readonly = true;
		}

		echo $customerId->input;
		?>
	</td>
	<td>
		<?php
		$customerName = FieldHelper::resetGroup($form->getField('id_number'), $group);

		if (!$data->isNew)
		{
			$customerName->readonly = true;
		}

		echo $customerName->input;
		?>
	</td>
	<td>
		<?php
		$birthDate = FieldHelper::resetGroup($form->getField('birth_date'), $group);

		if (!$data->isNew)
		{
			$birthDate->readonly = true;
		}

		echo $birthDate->input;
		?>
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
		(1)<span class="emptydisplay1"><?php echo substr($field1st->value, 5); ?></span>
		<br />
		(2)<span class="emptydisplay2"><?php echo substr($field2nd->value, 5); ?></span>

		<?php echo $field1st->input; ?>
		<?php echo $field2nd->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('method'), $group)->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('fileuploadplaceholder'), $group)->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('note'), $group)->input; ?>
	</td>
<?php if ($data->isNew): ?>
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
<?php endif; ?>
</tr>
