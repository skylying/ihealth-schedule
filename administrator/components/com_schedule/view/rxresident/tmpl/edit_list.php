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

$jsOption = [
	'customerApi' => JRoute::_('index.php?option=com_schedule&task=customer.ajax.json&institute_id=', false),
	'isEdit' => $data->isEdit,
];

?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		RxResidentEditList.run(<?php echo json_encode($jsOption); ?>);
	});
</script>

<form id="adminForm" name="adminForm" action="" method="post" class="form-horizontal">
	<div id="institute-information" class="row-fluid">
		<div class="col-md-4">
			<?php
			$instituteIdSelection = $instituteForm->getField('institute_id_selection');

			if ($data->isEdit)
			{
				$instituteIdSelection->readonly = true;
			}

			echo $instituteIdSelection->getControlGroup();
			?>
			<?php echo $instituteForm->getField('institute_id')->getControlGroup(); ?>
		</div>
		<div class="col-md-4 col-md-offset-1">
			<?php echo $instituteForm->getField('floor')->getControlGroup(); ?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="col-md-4 deliveryblock">
			<div class="weekday">
				外送日：
				<span id="weekday-from-js">
					<?php echo $data->institute['delivery_weekday']; ?>
					<?php echo $instituteForm->getField('delivery_weekday')->input; ?>
				</span>
			</div>
			<div>
				<?php echo \Schedule\Helper\ColorHelper::getColorBlock($data->institute['color_hex'], 30, 'delivery-color'); ?>
				<?php echo $instituteForm->getField('color_hex')->input; ?>
			</div>
		</div>

		<div class="col-md-4 col-md-offset-1">
			總數： <span id="total-row"><?php echo count($forms); ?></span> 筆處方箋
		</div>
	</div>

	<div class="row-fluid">
		<div class="col-md-12">
			<div>
				備註：
				<span id="note-from-js">
					<?php echo $data->institute['note']; ?>
				</span>
			</div>
		</div>
	</div>

	<hr />
	<?php if (! $data->isEdit): ?>
		<p>
			<button type="button" class="btn btn-primary button-add-row" value="1">
				<span class="glyphicon glyphicon-plus"></span>
				新增 1 筆
			</button>
			<button type="button" class="btn btn-primary button-add-row" value="5">
				<span class="glyphicon glyphicon-plus"></span>
				新增 5 筆
			</button>
			<button type="button" class="btn btn-primary button-add-row" value="10">
				<span class="glyphicon glyphicon-plus"></span>
				新增 10 筆
			</button>
		</p>
	<?php endif; ?>

	<table class="table table-striped" id="rx-list">
		<thead>
			<tr>
				<th width="11%">客戶</th>
				<th width="10%">身分證字號</th>
				<th width="9.7%">生日</th>
				<th width="9.7%">就醫日期</th>
				<th width="5.87%">給藥天數</th>
				<th width="5.26%">可調劑次數</th>
				<th width="6%">處方箋外送次數</th>
				<th width="6.57%">藥吃完日</th>
				<th width="7.1%">處方箋取得方式</th>
				<th width="9.82%">備註</th>
				<?php if (! $data->isEdit): ?>
				<th width="8.33%">複製/刪除</th>
				<?php endif; ?>
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

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script id="row-template" class="hide" type="text/html">
	<?php echo $this->loadTemplate('row', array('group' => 'items.0hash0', 'form' => $templateForm)); ?>
</script>
