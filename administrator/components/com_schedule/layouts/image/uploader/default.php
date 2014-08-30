<?php
/**
 * @var Windwalker\Data\Data    $displayData
 * @var JFormFieldImageUploader $field
 */
$data = $displayData;
$field = $data['field'];
?>

<div id="<?php echo $data['containerId']; ?>">
	<div class="hide">
		<?php echo $data['fileInput']; ?>
		<?php echo $data['input']; ?>
	</div>
	<div class="pull-left">
		<span class="thumb-loading">
			載入縮圖...
			<!-- TODO: Loading progress bar -->
		</span>
		<span class="thumb"></span>
	</div>
	<div class="pull-left" style="margin-left: 10px;">
		<button type="button" class="btn btn-primary browse-button">選擇圖片</button>
		<button type="button" class="btn btn-danger delete-button">刪除</button>
	</div>
	<div class="clearfix"></div>
</div>

<script type="text/javascript">
(function()
{
	var options = <?php echo json_encode($data['jsOptions']); ?>;

<?php if (!empty($field->after_upload)): ?>
	options.afterUpload = <?php echo $field->after_upload; ?>;
<?php endif; ?>

<?php if (!empty($field->after_remove)): ?>
	options.afterRemove = <?php echo $field->after_remove; ?>;
<?php endif; ?>

<?php if (!empty($field->upload_extra_data)): ?>
	options.uploadExtraData = <?php echo $field->upload_extra_data; ?>;
<?php endif; ?>

	var uploader = new ImageUploader('<?php echo $data['containerId']; ?>', options);

	uploader.init();

<?php if (!empty($data['image'])): ?>
	uploader.displayThumb(<?php echo json_encode($data['image']); ?>);
<?php endif; ?>
})();
</script>
