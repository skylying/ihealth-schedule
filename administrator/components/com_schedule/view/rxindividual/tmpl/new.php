<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JHtmlBootstrap::tooltip();
JHtmlFormbehavior::chosen('select');
JHtmlBehavior::formvalidation();

$basic = $data->form->getFieldset("basic");
$ps    = $data->form->getFieldset("schedules_ps");

$customerID = $data->form->getField('customer_id')->id;
$telOfficeID = $data->form->getField('tel_office')->id;
$telHomeID = $data->form->getField('tel_home')->id;
$mobileID = $data->form->getField('mobile')->id;

?>

<script type="text/javascript">
	var telOfficeID	= "<?php echo $telOfficeID;?>";
	var telHomeID 	= "<?php echo $telHomeID;?>";
	var mobileID 	= "<?php echo $mobileID;?>";

	(function($){
		$.fn.customerAjax = function(id) {
			var $that = $(this);
			return this.each(function() {
				jQuery.ajax({
					type:"POST",
					url: "index.php?option=com_schedule&task=customer.phone.json&id=" + id
				}).done(function(cdata){
					var cdata 		= jQuery.parseJSON( cdata );
					var tel_office  = jQuery.parseJSON(cdata.tel_office);
					var tel_home 	= jQuery.parseJSON(cdata.tel_home);
					var mobile 		= jQuery.parseJSON(cdata.mobile);

					$.fn.customerAjax.updateHtml( telOfficeID, tel_office);
					$.fn.customerAjax.updateHtml( telHomeID, tel_home);
					$.fn.customerAjax.updateHtml( mobileID, mobile);
				});
			});
		};
		$.fn.customerAjax.updateHtml = function( tagID , telJson ){

			//Clear target hook html first.
			$('#'+ tagID).find('.controls').html("");

			for (var i = 0; i < telJson.length ; i++  )
			{
				//Set Defalut radio button
				var html = '<input type="radio" name="default_' + tagID + '_" ';
				if (telJson[i].default == 'true')
				{
					html += "checked/>";
				}
				else
				{
					html += ">";
				}

				// Set Numbers
				html += '<input type="text" name="number_' +
						tagID +
						'" ' +
						'value="' +
						telJson[i].number +
						'"/><br>';

				$('#'+ tagID).find('.controls').append(html);
			}
		};
	})(jQuery);

	jQuery(document).ready( function(){

		var customerDropDown = jQuery("#" + "<?php echo $customerID;?>");

		customerDropDown.customerAjax(2);

		customerDropDown.on('change', function(){
			jQuery(this).customerAjax(jQuery(this).val()) ;
		});
	});
</script>

<style>
	.schedules .control-label
	{
		float: none;
	}

	.schedules .controls
	{
		margin-left: 0;
	}

	.schedules input
	{
		width: 80%;
	}

	.schedules select
	{
		width: 100%;
	}

	.address label
	{
		display: none;
	}
</style>

<form name="adminForm" id="adminForm" method="post" action="<?php echo JURI::getInstance(); ?>" class="form-horizontal">
	<div class="row-fluid">
		<div class="col-lg-5">
			<?php
			foreach ($basic as $field)
			{
				echo $field->getControlGroup();
			}
			?>
		</div>
		<div class="col-lg-5 col-lg-offset-1">
			<?php foreach (array("1st", "2nd", "3rd") as $key): ?>
				<?php $schedules = $data->form->getGroup("schedules_{$key}"); ?>
				<div id="schedules_<?php echo $key; ?>" class="row-fluid schedules schedules_<?php echo $key; ?>">
					<div class="col-lg-3">
						<!-- TODO: 換成可愛的圓圈圈 -->
						<?php echo $schedules["jform_schedules_{$key}_deliver_nths"]->getControlGroup(); ?>
					</div>
					<div class="col-lg-9">
						<div class="row-fluid address">
							<div class="col-lg-12">
								<?php echo $schedules["jform_schedules_{$key}_address"]->getControlGroup(); ?>
							</div>
						</div>
						<div class="row-fluid">
							<div class="col-lg-4">
								<!-- TODO:js -->
								<?php echo $schedules["jform_schedules_{$key}_empty_date"]->getControlGroup(); ?>
							</div>
							<div class="col-lg-4">
								<?php echo $schedules["jform_schedules_{$key}_send_date"]->getControlGroup(); ?>
							</div>
							<div class="col-lg-4">
								<?php echo $schedules["jform_schedules_{$key}_send_time"]->getControlGroup(); ?>
							</div>
						</div>
					</div>
                </div>
			<?php endforeach; ?>
			<div class="row-fluid well">
				<div class="col-lg-12">
					<!-- TODO:js -->
					<div id="<?php echo $telOfficeID;?>" class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('tel_office'); ?>
						</div>
						<div class="controls">
							<input type="text" />
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<!-- TODO:js -->
					<div id="<?php echo $telHomeID;?>" class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('tel_home'); ?>
						</div>
						<div class="controls">
							<input type="text" />
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<!-- TODO:js -->
					<div id="<?php echo $mobileID;?>" class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('mobile'); ?>
						</div>
						<div class="controls">
							<input type="text" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <input type="hidden" name="option" value="com_schedule" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>