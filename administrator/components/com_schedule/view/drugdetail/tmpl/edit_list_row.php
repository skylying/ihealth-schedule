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

$ice    = FieldHelper::resetGroup($form->getField('ice'), $group);
$sorted = FieldHelper::resetGroup($form->getField('sorted'), $group);
$price  = FieldHelper::resetGroup($form->getField('price'), $group);
?>
<tr>
	<td colspan="8">

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
