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
 * @var $container     Windwalker\DI\Container
 * @var $data          Windwalker\Data\Data
 * @var $templateForm  JForm
 * @var $forms         JForm[]
 */
$container     = $this->getContainer();
$instituteForm = $data->instituteForm;
$templateForm  = $data->templateForm;
$forms         = $data->forms;
?>

<form id="adminForm" name="adminForm" action="" method="post" class="form-horizontal">
	<div id="institute-information" class="row-fluid">
		<div class="col-lg-8">
			<?php echo $instituteForm->getField('institute_id')->getControlGroup(); ?>
		</div>
		<div class="col-lg-4">
			<?php echo $instituteForm->getField('floor')->getControlGroup(); ?>
		</div>
	</div>

	<table class="table table-striped" id="rx-list">
		<thead>
			<tr>
				<th>編號</th>
				<th>客戶</th>
				<th>身分證字號</th>
				<th>生日</th>
				<th>就醫日期</th>
				<th>給藥天數</th>
				<th>可調劑次數</th>
				<th>處方箋外送次數</th>
				<th>藥吃完日</th>
				<th>處方箋取得方式</th>
				<th>備註</th>
				<th>複製/刪除</th>
			</tr>
		</thead>
		<tbody>
<?php if (count($forms) > 0): ?>
	<?php
	foreach ($forms as $id => $form)
	{
		$group = 'item.old.' . $id;

		echo $this->loadTemplate('row', array('group' => $group, 'form' => $form));
	}
	?>
<?php endif; ?>
		</tbody>
	</table>

	<p>
		一次新增 :

		<input type="text" value="1" id="new-row-number" />

		<button type="button" class="btn button-delete-row" onclick="addNewRow(jQuery('#new-row-number').val());">
			<span class="glyphicon glyphicon-plus"></span>
			新增
		</button>
	</p>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script id="row-template" class="hide" type="text/html">
	<?php echo $this->loadTemplate('row', array('group' => 'item.new.{{hash}}', 'form' => $templateForm)); ?>
</script>

<script type="text/javascript">
	function addNewRow(amount)
	{
		var i,
			html = '',
			hash = '',
			newHash = '',
			$table = jQuery('#rx-list').find('tbody');

		amount = isNaN(amount) ? 0 : amount;

		for (i = 0; i < amount; ++i)
		{
			newHash = (new Date).getTime().toString();
			hash = (hash === newHash) ? newHash + '1' : newHash;

			html = jQuery('#row-template').clone().html();
			html = html.replace('{{hash}}', hash);

			$table.append(html);
		}
	}
</script>
