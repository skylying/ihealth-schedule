<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * drug detail layout
 *
 * @var array $displayData
 * @var JForm $form
 * @var int   $formId
 *
 * $displayData 變數詳細內容如下
 * ```php
 * array(
 *     'form' => \JForm()  // drugdetailfilter form
 * )
 * ```
 */
$data = $displayData;

$form = $data['form'];
$formId = empty($data['formId']) ? 'adminForm' : $data['formId'];

$fieldsets = $form->getFieldset("filter");
?>
<form name="<?php echo $formId; ?>"
	id="<?php echo $formId; ?>"
	action="<?php echo JRoute::_('index.php'); ?>"
	method="get" class="form-horizontal">
	<div class="control-group">
		<div class="control-label">
			預計外送日:
		</div>
		<input type="hidden" name="option" value="com_schedule"/>
		<input type="hidden" name="view" value="drugdetail"/>
		<input type="hidden" name="layout" value="edit"/>

		<div class="controls">
			<?php echo $fieldsets['date_start']->input; ?>
			~
			<?php echo $fieldsets['date_end']->input; ?>
		</div>
	</div>

	<?php echo $fieldsets['senderIds']->getControlGroup(); ?>

	<?php echo JHtml::_('form.token'); ?>
</form>
