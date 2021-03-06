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
use Windwalker\Joomla\DataMapper\DataMapper;

/**
 * Prepare data for this template.
 *
 * @var $data Windwalker\Data\Data
 * @var $form JForm
 */
$form   = $data->form;
$group  = $data->group;
$taskId = $data->task_id;
$extra  = isset($data->extra) ? $data->extra : (new Data);
$class  = isset($data->class) ? "class=\"{$data->class}\"" : "";
$id     = isset($data->id) ? "id=\"{$data->id}\"" : "";
$isJs   = isset($data->isJs) ? $data->isJs : false;

$idValue     = $extra->id;
$sortedValue = $extra->sorted;
$iceValue    = $extra->ice;
$priceValue  = $extra->price;

if ($isJs)
{
	$idValue     = null;
	$sortedValue = null;
	$iceValue    = null;
	$priceValue  = null;

	$group = "js_{$group}";
}

$idInput = FieldHelper::resetGroup($form->getField('id', null, $idValue), $group);
$task    = FieldHelper::resetGroup($form->getField('task_id', null, $taskId), $group);
$sorted  = FieldHelper::resetGroup($form->getField('sorted', null, $sortedValue), $group);
$ice     = FieldHelper::resetGroup($form->getField('ice', null, $iceValue), $group);
$price   = FieldHelper::resetGroup($form->getField('price', null, (int) $priceValue), $group);

?>

<tr <?php echo $id; ?> <?php echo $class; ?> data-id="noid">
	<td colspan="9">
		<?php echo $idInput->input; ?>
		<?php echo $task->input; ?>
	</td>
	<td colspan="3" class="text-center">機構自訂</td>
	<td>
		<?php echo $price->input; ?>
	</td>
	<td>
		<?php
		if ($isJs)
		{
			echo '<button class="row-remove-button btn btn-danger" type="button">刪除</button>';
		}?>
	</td>
</tr>
