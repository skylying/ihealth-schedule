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
	<td class="hide">
		<?php echo $id->input; ?>
	</td>
	<td>
		<?php
		$customerId = FieldHelper::resetGroup($form->getField('customer_id_selection'), $group);

		if ($data->isEdit)
		{
			$customerId->readonly = true;
		}

		echo $customerId->input;
		echo FieldHelper::resetGroup($form->getField('customer_id'), $group)->input;
		?>
	</td>
	<td>
		<?php
		$customerName = FieldHelper::resetGroup($form->getField('id_number'), $group);

		if ($customerId->value > 0)
		{
			$customerName->readonly = true;
		}

		echo $customerName->input;
		?>
	</td>
	<td>
		<?php
		$birthDate = FieldHelper::resetGroup($form->getField('birth_date'), $group);

		if ($customerId->value > 0)
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
		$emptyDate1st = FieldHelper::resetGroup($form->getField('empty_date_1st'), $group);
		$emptyDate2nd = FieldHelper::resetGroup($form->getField('empty_date_2nd'), $group);
		?>

		(1)
		<span class="drug-empty-date-text1">
			<?php echo substr($emptyDate1st->value, 5); ?>
		</span>
		<br />

		(2)
		<span class="drug-empty-date-text2">
			<?php echo substr($emptyDate2nd->value, 5); ?>
		</span>

		<?php echo $emptyDate1st->input; ?>
		<?php echo $emptyDate2nd->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('method'), $group)->input; ?>
	</td>
	<td>
		<?php echo FieldHelper::resetGroup($form->getField('note'), $group)->input; ?>
	</td>
<?php if (! $data->isEdit): ?>
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
