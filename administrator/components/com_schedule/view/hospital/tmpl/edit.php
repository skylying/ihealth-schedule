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

/**
 * Prepare data for this template.
 *
 * @var $container Windwalker\DI\Container
 * @var $data      Windwalker\Data\Data
 * @var $item      \stdClass
 */
$container = $this->getContainer();
$form = $data->form;
$item = $data->item;

$fieldsets = $data->form->getFieldsets();
$fieldset = $fieldsets['information'];

$cityId 	 = $form->getField('city')->id;
$cityTitleId = $form->getField('city_title')->id;
$areaId 	 = $form->getField('area')->id;
$areaTitleId = $form->getField('area_title')->id;
?>
<!-- Validate Script -->
<script type="text/javascript">
	Joomla.submitbutton = function (task)
	{
		if (task == 'hospital.edit.cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<div id="schedule" class="windwalker hospital edit-form row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data">
		<div class="row-fluid">
			<div class="span8">
				<fieldset id="sender-edit-fieldset-<?php echo $fieldset->name ?>" class="form-horizontal">
					<?php foreach ($data->form->getFieldset($fieldset->name) as $field): ?>
						<div id="control_<?php echo $field->id; ?>">
							<?php echo $field->getControlGroup() . "\n\n"; ?>
						</div>
					<?php endforeach;?>
				</fieldset>
			</div>
		</div>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>

<script type="text/javascript">
	(function ($, window)
	{
		/**
		 * Class Address
		 *
		 * @returns {void}
		 */
		function Address(fieldIdList)
		{
			if (!fieldIdList['city'])
			{
				return;
			}

			// Default field id list
			this.fieldIdList = {
				"city": null,
				"cityTitle": null,
				"area": null,
				"areaTitle": null
			};

			$.extend(this.fieldIdList, fieldIdList);

			this.$cityValue = $('#' + fieldIdList['city']);
			this.$cityTitle = (fieldIdList['cityTitle'] ? $('#' + fieldIdList['cityTitle']) : null);
			this.$areaValue = (fieldIdList['area'] ? $('#' + fieldIdList['area']) : null);
			this.$areaTitle = (fieldIdList['areaTitle'] ? $('#' + fieldIdList['areaTitle']) : null);

			this.bind();

			if (this.$areaValue)
			{
				this.generateAreaSelector();
			}
		}

		/**
		 * Bind field events
		 */
		Address.prototype.bind = function ()
		{
			var self = this;

			if (self.$cityTitle)
			{
				// Update city title when city value changed
				self.$cityValue.change(function ()
				{
					self.$cityTitle.val(self.getCityValue() > 0 ? self.$cityValue.find("option:selected").text() : '');
				});
			}

			if (self.$areaValue)
			{
				self.$cityValue.css('margin-right', '5px');

				// Initialize area selector when city value changed
				self.$cityValue.change(function ()
				{
					$('#' + self.getAreaSelectorId()).remove();
					self.$areaValue.val('');
					self.$areaTitle.val('');

					// Create area selector
					if (self.getCityValue() > 0)
					{
						self.generateAreaSelector();
					}
				});
			}
		};

		/**
		 * Generate area selector
		 *
		 * @returns {void}
		 */
		Address.prototype.generateAreaSelector = function ()
		{
			var self = this,
				$areaClone = $('#city-' + self.getCityValue().toString()).clone();

			$areaClone.attr("id", self.getAreaSelectorId());

			$areaClone.val(self.$areaValue.val());

			$areaClone.change(function ()
			{
				var value = $(this).val();

				// Convert area value to integer
				value = isNaN(value) ? 0 : parseInt(value);

				self.$areaValue.val(value);
				self.$areaTitle.val(value > 0 ? $areaClone.find("option:selected").text() : '');
			});

			$areaClone.insertAfter(self.$cityValue);
		};

		/**
		 * Get selected city value
		 *
		 * @returns {int}
		 */
		Address.prototype.getCityValue = function ()
		{
			var value = this.$cityValue.val();

			// Convert city value to integer
			value = isNaN(value) ? 0 : parseInt(value);

			return value;
		};

		/**
		 * Get area selector element id
		 *
		 * @returns {string}
		 */
		Address.prototype.getAreaSelectorId = function ()
		{
			return this.fieldIdList.city + '-area';
		};

		window.Address = window.Address || Address;
	})(jQuery, window);

	var liveCity = new Address({
		city: '<?php echo $cityId; ?>',
		cityTitle: '<?php echo $cityTitleId; ?>',
		area: '<?php echo $areaId; ?>',
		areaTitle: '<?php echo $areaTitleId; ?>'
	});
</script>
