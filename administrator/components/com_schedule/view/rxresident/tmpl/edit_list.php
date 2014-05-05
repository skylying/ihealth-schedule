<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Prepare data for this template.
 *
 * @var $container    Windwalker\DI\Container
 * @var $data         Windwalker\Data\Data
 * @var $templateForm JForm
 * @var $forms        JForm[]
 * @var $asset        Windwalker\Helper\AssetHelper
 */
$container     = $this->getContainer();
$instituteForm = $data->instituteForm;
$templateForm  = $data->templateForm;
$forms         = $data->forms;
$asset         = $container->get('helper.asset');

JHtmlJquery::framework(true);

$asset->addCSS('rxresident.css');
$asset->addJS('multi-row-handler.js');
$asset->addJS('rxresident/edit-list.js');

JHtml::stylesheet('com_schedule/rxresident.css', false, true);

?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		RxResidentEditList.run();
	});
</script>

<form id="adminForm" name="adminForm" action="" method="post" class="form-horizontal">
	<div id="institute-information" class="row-fluid">
		<div class="col-md-4">
			<?php echo $instituteForm->getField('institute_id')->getControlGroup(); ?>
		</div>
		<div class="col-md-4 col-md-offset-1">
			<?php echo $instituteForm->getField('floor')->getControlGroup(); ?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="col-md-3 deliveryblock">
			<div class="weekday">外送日：<span id="weekday-from-js"></span></div>
			<div>
				<?php echo \Schedule\Helper\ColorHelper::getColorBlock('#ffffff', 30, 'deliverycolor'); ?>
			</div>
		</div>
	</div>

	<table class="table table-striped" id="rx-list">
		<thead>
			<tr>
				<th width="4%">編號</th>
				<th width="7%">客戶</th>
				<th width="12%">身分證字號</th>
				<th width="8%">生日</th>
				<th width="8%">就醫日期</th>
				<th width="7%">給藥天數</th>
				<th width="6%">可調劑次數</th>
				<th width="7%">處方箋外送次數</th>
				<th width="8%">藥吃完日</th>
				<th width="12%">處方箋取得方式</th>
				<th width="9%">處方箋上傳</th>
				<th width="8%">備註</th>
				<th width="4%">複製/刪除</th>
			</tr>
		</thead>
		<tbody>
<?php
if (count($forms) > 0)
{
	foreach ($forms as $hash => $form)
	{
		$group = 'items.' . $hash;

		echo $this->loadTemplate('row', array('group' => $group, 'form' => $form));
	}
}
?>
		</tbody>
	</table>

	<p>
		一次新增 :

		<input type="text" value="1" id="new-row-number" />

		<button type="button" class="btn btn-default button-add-row">
			<span class="glyphicon glyphicon-plus"></span>
			新增
		</button>
	</p>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script id="row-template" class="hide" type="text/html">
	<?php echo $this->loadTemplate('row', array('group' => 'items.0hash0', 'form' => $templateForm)); ?>
</script>
