<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$container = $this->getContainer();
$asset = $container->get('helper.asset');
$form = $data->form;

JHtmlJquery::framework(true);

$asset->addJS('drugdetail/institute-extra.js');
?>

<script>
	jQuery(function()
	{
		window.InstituteExtraObject = new InstituteExtra("add-institute-extra", "row-remove-button", "row-institute-");
	});
</script>

<h3 class="text-right">
	<?php echo $data->date; ?>
</h3>

<?php foreach ($data->items as $sender): ?>
<h3>
	<?php echo $sender['name']; ?>
</h3>

<table id="drug-details" class="table table-bordered">
	<thead>
	<tr>
		<th>
			排程編號
		</th>
		<th>
			處方箋編號
		</th>
		<th>
			新增處方箋日
		</th>
		<th>
			吃完藥日
		</th>
		<th>
			所屬機構/會員
		</th>
		<th>
			縣市
		</th>
		<th>
			區域
		</th>
		<th>
			客戶
		</th>
		<th>
			完成分藥
		</th>
		<th>
			冰品
		</th>
		<th>
			自費金額
		</th>
		<th>
			最後編輯者
		</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($sender['institutes'] as $instituteId => $institute): ?>
		<?php foreach ($institute['schedules'] as $schedule): ?>
			<?php echo $this->loadTemplate('list_row', array('schedule' => $schedule)); ?>
		<?php endforeach; ?>

		<!-- Load database drug extra input -->
		<?php foreach ($institute['extra'] as $extra): ?>
			<?php echo $this->loadTemplate('extra_list_row', array('extra' => $extra, 'task_id' => $sender['task_id'], 'group' => "institutes.{$instituteId}.{$extra->id}")); ?>
		<?php endforeach; ?>

		<!-- Javascript drug extra input -->
		<?php
		echo $this->loadTemplate('extra_list_row', array(
			'id'      => "row-institute-{$instituteId}",
			'task_id' => $sender['task_id'],
			'class'   => 'hide',
			'group'   => "institutes.{$instituteId}.0hash0",
			'isJs'    => true)
		);
		?>
	<tr>
		<td colspan="11" class="text-right"><!-- TODO: 份數 --> 份</td>
		<td>
			<button class="add-institute-extra" data-institute-id="<?php echo $instituteId; ?>" type="button">+</button>
		</td>
	</tr>
	<?php endforeach; ?>

	<?php foreach ($sender['individuals'] as $schedule): ?>
		<?php echo $this->loadTemplate('list_row', array('schedule' => $schedule)); ?>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endforeach; ?>
