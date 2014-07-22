<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Schedule\Helper\AreaHelper;

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
		table th, table td {
			border:1px solid #000000 !important;
		}
		body.component {
			padding-top: 30px;
			margin-top: -30px;
		}
	}
	');
}

?>

	<style>

	</style>

<div class="head-div center">
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
</div>


<table class="table table-bordered">
	<tr>
		<th class="center" width="8%">機構/散客</th>
		<th class="center">地址</th>
		<th class="center" width="12%">備註</th>
		<th class="center" width="5%">份數</th>
		<th class="center" width="12%">電話</th>
		<th class="center" width="12%">冰品</th>
		<th class="center" width="8%">缺ID份數</th>
		<th class="center" width="8%">自費小計</th>
		<th class="center" width="8%">宅配時段</th>
	</tr>
	<!--第一層分類 : schedule 類別, institutes or customers-->
	<?php foreach ($data->item->schedules as $type => $schedulesByType): ?>

		<!--第二層分類 : 排程區域-->
		<?php foreach ($schedulesByType as $area => $schedulesByArea): ;?>

			<tr>
				<td colspan="9">
					<?php echo AreaHelper::getAreaTitle($area); ?>
				</td>
			</tr>

			<!--第三層：單筆排程-->
			<?php foreach ($schedulesByArea as $schedule): ;?>
				<tr>
					<!--機構/散客-->
					<td class="<?php echo $type == 'customers' ? 'alert-info' : 'alert-warning'; ?>">
						<?php echo $schedule['title']; ?>
					</td>

					<!--地址-->
					<td>
						<?php echo $schedule['address']; ?>
					</td>

					<!--備註-->
					<td>
						<?php
						foreach ($schedule['notes'] as $note)
						{
							echo $note['type'] . (empty($note['type']) ? '' : ': ') . $note['message'] . '<br />';
						}
						?>
					</td>

					<!--份數-->
					<td class="center">
						<?php echo $schedule['quantity']; ?>
					</td>

					<!--電話-->
					<td class="center">
						<?php echo implode('<br />', $schedule['phones']); ?>
					</td>

					<!--冰品-->
					<td class="center">
						<?php
						foreach ($schedule['ices'] as $ice)
						{
							echo $ice['drug_empty_date'] . ' ' . $ice['customer_name'] . '<br />';
						}
						?>
					</td>

					<!--缺id份數-->
					<td class="center"><!--todo:缺 id 份數--></td>

					<!--自費金額-->
					<td class="center">
						<?php echo $type == 'customers' ? $schedule['expense'] : $schedule['extraExpenses']; ?>
					</td>

					<!--時段-->
					<td class="center">
						<?php echo $type == 'customers' ? $schedule['session'] : '-'; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php endforeach; ?>
</table>

<?php if ($componentTmpl): ?>
<script type="text/javascript">
	print();
</script>
<?php endif;
