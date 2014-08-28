<?php
/**
 * @var Windwalker\Data\Data $displayData
 */
$data = $displayData;
?>

<div id="<?php echo $data['containerId']; ?>">
	<div class="hide">
		<?php echo $data['input']; ?>
	</div>
	<div class="pull-left">
		<span class="thumb-loading" style="display: none;">
			載入縮圖...
			<!-- TODO: Loading progress bar -->
		</span>
		<span class="thumb"></span>
	</div>
	<div class="pull-left" style="margin-left: 10px;">
		<button type="button" class="btn btn-primary browse-button">選擇圖片</button>
	</div>
	<div class="clearfix"></div>
</div>

<script type="text/javascript">
(function()
{
	var options = <?php echo json_encode($data['jsOptions']); ?>;
	var file = new ImageFile('<?php echo $data['containerId']; ?>', options);

	file.init();
})();
</script>
