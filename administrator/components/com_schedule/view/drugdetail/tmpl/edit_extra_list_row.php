<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Schedule\Helper\Mapping\MemberCustomerHelper;
use Windwalker\Data\Data;
use Schedule\Helper\Form\FieldHelper;

/**
 * Prepare data for this template.
 *
 * @var $data Windwalker\Data\Data
 * @var $form JForm
 */
$form  = $data->form;
$group = $data->group;
$extra = isset($data->extra) ? $data->extra : (new Data);
$class = isset($data->class) ? "class=\"{$data->class}\"" : "";
$id    = isset($data->id) ? "id=\"{$data->id}\"" : "";

$sorted = FieldHelper::resetGroup($form->getField('sorted', null, $extra->sorted), $group);
$ice    = FieldHelper::resetGroup($form->getField('ice', null, $extra->ice), $group);
$price  = FieldHelper::resetGroup($form->getField('price', null, $extra->price), $group);
?>

<tr <?php echo $id; ?> <?php echo $class; ?>>
	<td colspan="8">
		<?php
		$sorted = FieldHelper::resetGroup($form->getField('sorted', null, $extra->sorted), $group);
		$ice    = FieldHelper::resetGroup($form->getField('ice', null, $extra->ice), $group);
		$price  = FieldHelper::resetGroup($form->getField('price', null, $extra->price), $group);
		?>
	</td>
	<td>
		<?php echo $ice->input; ?>
	</td>
	<td>
		<?php echo $sorted->input; ?>
	</td>
	<td>
		<?php echo $price->input; ?>
	</td>
	<td>

	</td>
</tr>
