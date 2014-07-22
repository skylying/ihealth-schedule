<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Prepare data for this template.
 *
 * @var $container Windwalker\DI\Container
 * @var $asset     Windwalker\Helper\AssetHelper
 * @var $data      Windwalker\Data\Data
 * @var $item      stdClass
 */
$container = $this->getContainer();
$asset     = $container->get('helper.asset');
$item      = $data->item;

$componentTmpl = ('component' === JUri::getInstance()->getVar('tmpl'));

if ($componentTmpl)
{
	$asset->internalCSS('
	@media print {
		@page{
			size: landscape;
		}
		body.component {
			padding-top: 30px;
			margin-top: -30px;
		}
	}
	');
}
?>

<h2>
	<?php echo $item->date; ?>
	<?php echo $item->sender_name; ?>
	藥師外送路線
</h2>
<h3>
	機構點數: <span class="text-primary"><?php echo $item->instituteQuntity; ?>家</span>
	散客點數: <span class="text-primary"><?php echo $item->customerQuntity; ?>戶</span>
</h3>
<h3>合計: <?php echo $item->totalQuntity; ?></h3>

<table class="table table-bordered">
	<tr>
		<th>機構/散客</th>
		<th>地址</th>
		<th>備註</th>
		<th>份數</th>
		<th>電話</th>
		<th>冰品</th>
		<th>自費份數</th>
		<th>自費小計</th>
		<th>宅配時段</th>
	</tr>
	<?php foreach ($data->item->schedules as $type => $schedules): ?>
		<?php foreach ($schedules as $schedule): ?>
	<tr>
		<td>
			<?php echo $schedule['title']; ?>
		</td>
		<td><?php echo $schedule['address']; ?></td>
		<td>
			<?php
			foreach ($schedule['notes'] as $note)
			{
				echo $note['type'] . (empty($note['type']) ? '' : ': ') . $note['message'] . '<br />';
			}
			?>
		</td>
		<td><?php echo $schedule['quantity']; ?></td>
		<td><?php echo implode('<br />', $schedule['phones']); ?></td>
		<td>
			<?php
			foreach ($schedule['ices'] as $ice)
			{
				echo $ice['drug_empty_date'] . ' ' . $ice['customer_name'] . '<br />';
			}
			?>
		</td>
		<td><?php echo count($schedule['expenses']); ?></td>
		<td>
			<?php
			foreach ($schedule['expenses'] as $expense)
			{
				echo $expense['customer_name'] . ': $' . $expense['price'] . '<br />';
			}
			?>

			<?php echo $schedule['extraExpenses']; ?>
		</td>
		<td><?php echo $schedule['session']; ?></td>
	</tr>
		<?php endforeach; ?>
	<?php endforeach; ?>
</table>

<?php if ($componentTmpl): ?>
<script type="text/javascript">
	print();
</script>
<?php endif;
