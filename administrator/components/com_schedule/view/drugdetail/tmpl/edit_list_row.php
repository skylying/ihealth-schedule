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
?>
<tr>
	<td colspan="8">

	</td>
	<td>
		<?php
		$ice = FieldHelper::resetGroup($form->getField('ice'), $group);

		echo $ice->input;
		?>
	</td>
	<td>
		<?php
		$sorted = FieldHelper::resetGroup($form->getField('sorted'), $group);

		echo $sorted->input;
		?>
	</td>
	<td>
		<?php
		$price = FieldHelper::resetGroup($form->getField('price'), $group);

		echo $price->input;
		?>
	</td>
	<td>

	</td>
</tr>
