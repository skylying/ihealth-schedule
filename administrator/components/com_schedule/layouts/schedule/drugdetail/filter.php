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

$fieldsets = $form->getFieldset("task");
?>
<form action="<?php echo JURI::getInstance(); ?>"  method="post" name="adminForm" id="adminForm"
	class="form-horizontal" enctype="multipart/form-data">
	<?php echo $fieldsets['date']->getControlGroup(); ?>

	<?php echo $fieldsets['senderIds']->getControlGroup(); ?>

	<?php echo JHtml::_('form.token'); ?>
</form>
