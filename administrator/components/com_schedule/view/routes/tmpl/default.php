<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\View\Layout\FileLayout;

// No direct access
defined('_JEXEC') or die;

// Prepare script
JHtmlBootstrap::tooltip();
JHtmlFormbehavior::chosen('select');
JHtmlDropdown::init();

/**
 * Prepare data for this template.
 *
 * @var Windwalker\DI\Container $container
 */
$container = $this->getContainer();

$doc = JFactory::getDocument();
$css = <<<CSS
.route-outer
{
	display:inline-block;
	border-radius: 5px;
	border: 1px solid #A8A6A6;
	margin-top:5px;
	opacity: 0.7;
}
.route-outer div
{
	padding: 0 5px;
}
.route-outer div label
{
	display:inline-block;
	padding-top:5px;
	padding-left:4px;
}
.route-outer div label:hover
{
	cursor: pointer;
}
.customer-bg
{
	background: #eea786;
}
.institute-bg
{
	background: #A8F8FF;
}
.mask
{
	width: 200px;
	height: 30px;
	position: absolute;
	opacity: 0;
}
.checkall
{
	display:none;
	text-align: center;
}
CSS;

$doc->addStyleDeclaration($css);

$routeUpdater = $this->data->filterForm->getGroup('routeupdater');
$senderForm   = $routeUpdater['routeupdater_sender_id'];
$weekdayForm  = $routeUpdater['routeupdater_weekday'];

?>

<div id="schedule" class="windwalker routes tablelist row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		<div id="j-main-container">

			<div class="row">
				<div class="col-md-1">
					<div class="route-outer institute-bg" style="padding: 4px">機構路線</div>
				</div>
				<div class="col-md-1">
					<div class="route-outer customer-bg" style="padding: 4px">散客路線</div>
				</div>
				<div class="col-md-3 col-md-offset-1">
					<?php echo $senderForm->getControlGroup(); ?>
				</div>
				<div class="col-md-3">
					<?php echo $weekdayForm->getControlGroup(); ?>
				</div>
				<div class="col-md-2 col-md-offset-1">
					<span id="uncheckall" class="btn btn-danger">取消選取所有</span>
				</div>
			</div>

			<hr />

			<?php echo $this->loadTemplate('overview'); ?>

			<?php echo with(new FileLayout('joomla.batchtools.modal'))->render(array('view' => $this->data, 'task_prefix' => 'routes.')); ?>

			<!-- Hidden Inputs -->
			<div id="hidden-inputs">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHtml::_('form.token'); ?>
			</div>

		</div>
	</form>
</div>

<script>

	//todo: move RouteJs to external file link
	(function($)
	{
		// Check global conflict
		if (typeof window.RouteJs !== "undefined")
		{
			return;
		}

		"use strict";

		// Register RouteJs
		window.RouteJs = {

			// Prepare all element we need
			initialize : function()
			{
				this.senderInput = $('#routeupdater_sender_id');
				this.weekInput   = $('#routeupdater_weekday');
				this.routeBlocks = $('.routeinput');
				this.hiddenArea  = $('#hidden-inputs');

				// Uncheckall button
				this.uncheckall  = $('#uncheckall');
				this.checkall    = $('.checkall');

				// Checkall button's mask
				this.mask        = $('.mask');

				// Register $this name space for routeJs self
				window.$this = this;

				// Bind all events we need
				$this.bindEvent();
			},

			/**
			 * Bind all HTML events we need
			 */
			bindEvent : function()
			{
				// Bind onchange event to sender dropdown list
				$this.senderInput.on('change', function()
				{
					var senderId = $(this).val();

					// @ 施工中
					//$this.updateHiddehInputs(senderId, null);
				});

				// Bind onchange event to weekday dropdown list
				$this.weekInput.on('change', function()
				{
					var weekDay = $(this).val();

					// @ 施工中
					//$this.updateHiddehInputs(null, weekDay);
				});

				// Bind onchange event to each routeBlock checkbox
				$this.routeBlocks.on('change', function()
				{
					var routeId = $(this).attr('id');

					// Create new input for each route
					if (this.checked)
					{
						var inputHtml = $this.createInputElement($(this));

						// Emphasize selected target
						$(this).closest('.route-outer').css('opacity', '1');

						// Append route hidden inputs
						$this.hiddenArea.append(inputHtml);
					}
					// If user uncheck route, remove input to be sent
					else
					{
						$(this).closest('.route-outer').css('opacity', '0.7');

						$this.hiddenArea.find('input[title="' + routeId + '"]').remove();
					}
				});

				// Bind uncheckall button event
				$this.uncheckall.on('click', function()
				{
					var allInputs = $('.routeinput');

					allInputs.each(function()
					{
						$(this).prop('checked', false);

						// execute routeBlocks onchange event
						$this.routeBlocks.trigger('change');
					})
				});

				// Bind checkall button event
				$this.mask.hover(

				// Hover in effect
				function()
				{
					$(this).css('opacity', '1');
					$(this).css('position', 'relative');
					$(this).find('.checkall').css('display', 'block');
				},

				// Hover out effect
				function()
				{
					//$(this).css('opacity', '0');
					//$(this).css('position', 'absolute');
					//$(this).find('.checkall').css('display', 'none');
				});

				// Bind checkall button event
				$this.checkall.on('click', function()
				{
					// Find all checkboxes in current <td>
					var checkboxes = $(this).closest('td').find('input[type="checkbox"]');

					$(checkboxes).prop('checked', true);

					// Execute routeBlocks onchange event
					$this.routeBlocks.trigger('change');
				});

				//TODO : uncheckall button (only for <td>)

			},

			/**
			 * Create <input> with route id as its value
			 *
			 * @param {object} checkbox
			 *
			 * @return HTML object
			 */
			createInputElement : function(checkbox)
			{
				// Get input attributes
				var attributes = $this.configureInput(checkbox);

				var input = $('<input/>', {
					'id'    : attributes.elementId,
					'type'  : attributes.type,
					'name'  : attributes.name,
					'title' : attributes.title,
					'class' : attributes.class,
					'value' : JSON.stringify(attributes.value)
				});

				return input;
			},

			/**
			 * Generate each unique input attributes
			 *
			 * @param {object} checkbox
			 *
			 * @returns object
			 */
			configureInput : function(checkbox)
			{
				var routeId       = checkbox.attr('id'),
					routeValueObj = $.parseJSON(checkbox.val());

				var date = new Date,
					timeStamp = date.getTime(),
					inputConfig = {};
					inputConfig.value = {};

				// Give each dynamically created input an unique id
				inputConfig.elementId = 'date-' + timeStamp;
				inputConfig.type      = 'hidden';
				inputConfig.name      = 'cid[]';
				inputConfig.title     = routeId;
				inputConfig.class     = 'hidden-route-inputs';

				// Put route value back
				inputConfig.value.id           = routeId;
				inputConfig.value.type         = routeValueObj.type;
				inputConfig.value.institute_id = routeValueObj.institute_id;

				return inputConfig;
			},

			/** @ 施工中
			 * Update all hidden input value
			 *
			 * @param {int}    senderId
			 * @param {string} weekDay
			 */
			updateHiddehInputs : function(senderId, weekDay)
			{
				var hiddenInputs = $('.hidden-route-inputs');

				hiddenInputs.each(function()
				{
					// 施工中
				})
			}
		};
	})(jQuery);

	// Initialize RouteJs
	jQuery(document).ready(function()
	{
		RouteJs.initialize();
	});
</script>