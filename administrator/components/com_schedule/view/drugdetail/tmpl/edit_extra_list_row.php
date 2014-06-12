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
$isJs  = isset($data->isJs) ? $data->isJs : false;

$sortedValue = $extra->sorted;
$iceValue    = $extra->ice;
$priceValue  = $extra->price;

if ($isJs)
{
	$sortedValue = null;
	$iceValue    = null;
	$priceValue  = null;
}

$sorted = FieldHelper::resetGroup($form->getField('sorted', null, $sortedValue), $group);
$ice    = FieldHelper::resetGroup($form->getField('ice', null, $iceValue), $group);
$price  = FieldHelper::resetGroup($form->getField('price', null, $priceValue), $group);
?>

<tr <?php echo $id; ?> <?php echo $class; ?>>
	<td colspan="8">
		&nbsp;
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
		&nbsp;
	</td>
</tr>
