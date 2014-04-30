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
 * @var $data         Windwalker\Data\Data
 * @var $templateForm JForm
 * @var $forms        JForm[]
 */
$instituteForm = $data->instituteForm;
$templateForm  = $data->templateForm;
$forms         = $data->forms;
?>
<form id="adminForm" name="adminForm" action="" method="post" class="form-horizontal">
	<div id="institute-information" class="row-fluid">
		<div class="col-lg-6">
			<?php echo $instituteForm->getField('institute_id')->getControlGroup(); ?>
		</div>
		<div class="col-lg-6">
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

		<button type="button" class="btn button-add-row" onclick="addNewRow(jQuery('#new-row-number').val());">
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
	(function ($)
	{
		var $tableBody = $('#rx-list').find('tbody');

		$('.button-add-row').click(function ()
		{
			var i,
				row = '',
				hash = '',
				newHash = '',
				amount = $('#new-row-number').val();

			amount = isNaN(amount) ? 0 : amount;

			for (i = 0; i < amount; ++i)
			{
				newHash = (new Date).getTime().toString();
				hash = (hash === newHash) ? newHash + '0' : newHash;

				row = $('#row-template').clone().html();
				row = row.replace(/{{hash}}/g, hash);
				row = row.replace(/__hash__/g, hash);

				row = $(row);

				row.find('.datetimepicker').datetimepicker({
					pickTime: false
				});

				$tableBody.append(row);
			}
		});

		$tableBody.on('click', '.button-delete-row', function ()
		{
			$(this).closest('tr').remove();
		});

		$tableBody.on('click', '.button-copy-row', function ()
		{
			var $row = $(this).closest('tr').clone(),
				hash = (new Date).getTime().toString(),
				idPrefix = $row.data('id-prefix'),
				namePrefix = $row.data('name-prefix'),
				idReplace = $row.data('id-replace').replace('{{hash}}', hash),
				nameReplace = $row.data('name-replace').replace('{{hash}}', hash);

			$row.find('[name^="' + namePrefix + '"]').each(function (i, node)
			{
				var id = $(this).attr('id'),
					name = $(this).attr('name'),
					newId = id.replace(idPrefix, idReplace),
					newName = name.replace(namePrefix, nameReplace);

				$(this).attr('id', newId);
				$(this).attr('name', newName);
			});

			$row.find('.datetimepicker').datetimepicker({
				pickTime: false
			});

			$tableBody.append($row);
		});
	})(jQuery);
</script>
