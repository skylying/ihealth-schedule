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
	var telOfficeID = "<?php echo $telOfficeID;?>";
	var telHomeID = "<?php echo $telHomeID;?>";
	var mobileID = "<?php echo $mobileID;?>";

	// Update empty rows of addresses inputs
	var addressesKeys = ["1st", "2nd", "3rd"];

	(function ($)
	{
		$.fn.customerAjax = function (id)
		{

			return this.each(function ()
			{
				if (!id)
				{
					// Update empty rows of phone inputs
					$.fn.customerAjax.updatePhoneHtml(telOfficeID);
					$.fn.customerAjax.updatePhoneHtml(telHomeID);
					$.fn.customerAjax.updatePhoneHtml(mobileID);

					// There are three column to update
					for (var i = 0; i < addressesKeys.length; i++)
					{
						$.fn.customerAjax.updateAddressHtml(addressesKeys[i], name);
					}
				}
				else
				{
					$.fn.customerAjax.fireAjax(id);
				}
			});
		};

		/**
		 * Fire ajax request and get from Customer model and Addresses modl
		 *
		 * fireAjax
		 *
		 * @param id
		 */
		$.fn.customerAjax.fireAjax = function (id)
		{
			// Fire ajax to Customer
			jQuery.ajax({
				type: "POST",
				url: "index.php?option=com_schedule&task=customer.ajax.json&id=" + id
			}).done(function (cdata)
				{
					var cdata      = jQuery.parseJSON(cdata);
					var tel_office = jQuery.parseJSON(cdata.tel_office);
					var tel_home   = jQuery.parseJSON(cdata.tel_home);
					var mobile     = jQuery.parseJSON(cdata.mobile);

					$.fn.customerAjax.updatePhoneHtml(telOfficeID, tel_office);
					$.fn.customerAjax.updatePhoneHtml(telHomeID, tel_home);
					$.fn.customerAjax.updatePhoneHtml(mobileID, mobile);
				});

			//Fire ajax to Addresses
			jQuery.ajax({
				type: "POST",
				url: "index.php?option=com_schedule&task=addresses.ajax.json&id=" + id
			}).done(function (cdata)
				{
					var cdata = jQuery.parseJSON(cdata);

					// Update empty rows of addresses inputs

					for (var i = 0; i < addressesKeys.length; i++)
					{
						$.fn.customerAjax.updateAddressHtml(addressesKeys[i], cdata);
					}
				});
		};

		/**
		 * Update address select list row
		 *
		 * updateAddressHtml
		 *
		 * @param key
		 * @param addressJson
		 */
		$.fn.customerAjax.updateAddressHtml = function (key, addressJson)
		{
			addressJson = addressJson || {};

			// ex: jform_schedule_1st_address
			var targetID = 'jform_schedules_' + key + '_address';

			// ex: jform[schedule_1st][address]
			var targetName = 'jform[' + 'schedule_' + key + '][address]';

			// Find its parent, later we will replace it with new select list
			var targetsParent = $('#' + targetID).parent();

			var html = '';

			// Add select tag
			html += '<select' +
				' name="' + targetName + '"' +
				' id="'   + targetID   + '">';

			for (var i = 0; i < addressJson.length; i++)
			{
				// Add option tag
				html += '<option' +
					' val="' + addressJson[i].id + '">' +
					addressJson[i].city_title +
					addressJson[i].area_title +
					addressJson[i].address +
					'</option>';
			}

			html += '</select>';

			//Clear target hook's html first.
			targetsParent.html("");

			targetsParent.html(html);
		};

		/**
		 * Update phone input list row
		 *
		 * updatePhoneHtml
		 *
		 * @param tagID
		 * @param telJson
		 */
		$.fn.customerAjax.updatePhoneHtml = function (tagID, telJson)
		{
			telJson = telJson || {};

			var target = $('#' + tagID);
			var defaultLength = telJson.length ? telJson.length : 3;

			//Clear target hook's html first.
			target.find('.controls').html("");

			//Update rows, append new input rows to target element
			for (var i = 0; i < defaultLength; i++)
			{
				var html = '';

				if (telJson[i] === undefined)
				{
					html += '<input type="radio" name="default_' + tagID + '"' + '/>';
					html += '<input type="text" name="number_'   + tagID + '"' + '/><br>'
				}
				else
				{
					//Set Defalut radio button
					html += '<input type="radio" name="default_' + tagID + '"';
					html += (telJson[i].default == 'true') ? ' checked/>' : '/>';

					// Set Numbers
					html += '<input type="text"' +
						' name="number_' + tagID + '"' +
						' value="' + (telJson[i].number ? telJson[i].number : '' ) +
						'"/><br>';
				}
				target.find('.controls').append(html);
			}
		};
	})(jQuery);

	jQuery(document).ready(function ()
	{

		// customer_id element id
		var customerDropDown = jQuery("#" + "<?php echo $customerID;?>");

		// customer_id value
		var customerID = "<?php $data->form->getInput('customer_id');?>";

		// If customer id is not set, select the first option
		if (!customerID)
		{
			customerID = customerDropDown.find('option:first').val();

			// Update once on load.
			customerDropDown.customerAjax(customerID);
		}

		customerDropDown.on('change', function ()
		{
			jQuery(this).customerAjax(jQuery(this).val());
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
					<div class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('tel_office'); ?>
						</div>
						<div class="controls">
							<input type="text"/>
						</div>
						<input type="hidden" id="<?php echo $telOfficeID;?>"/>
					</div>
				</div>
				<div class="col-lg-12">
					<!-- TODO:js -->
					<div class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('tel_home'); ?>
						</div>
						<div class="controls">
							<input type="text"/>
						</div>
						<input type="hidden" id="<?php echo $telHomeID;?>"/>
					</div>
				</div>
				<div class="col-lg-12">
					<!-- TODO:js -->
					<div class="control-group">
						<div class="control-label">
							<?php echo $data->form->getLabel('mobile'); ?>
						</div>
						<div class="controls">
							<input type="text"/>
						</div>
						<input type="hidden" id="<?php echo $mobileID;?>"/>
					</div>
				</div>
			</div>
		</div>
	</div>

    <input type="hidden" name="option" value="com_schedule" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>