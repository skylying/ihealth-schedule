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
	display: block;
}
.batchbutton
{
	display:none;
	text-align: center;
	color: #ffffff;
	border-radius: 4px;
	padding: 3px;
}
.batchbutton:hover
{
	cursor: pointer;
}
.checkall
{
	background: #5cb85c;
}
.uncheckall
{
	background: #d9534f;
}
CSS;

$doc->addStyleDeclaration($css);

$senderField  = $this->data->filterForm->getField('sender_id', 'routeupdater');
$weekdayField = $this->data->filterForm->getField('weekday', 'routeupdater');
?>

<div id="schedule" class="windwalker routes tablelist row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		<div id="j-main-container">
			<div class="row-fluid">
				<div class="col-md-2">
					<div class="route-outer institute-bg" style="padding: 4px">機構路線</div>
					<div class="route-outer customer-bg" style="padding: 4px">散客路線</div>
				</div>
				<div class="col-md-4 form-inline">
					<?php echo $senderField->label; ?>
					<?php echo $senderField->input; ?>
				</div>
				<div class="col-md-4 form-inline">
					<?php echo $weekdayField->label; ?>
					<?php echo $weekdayField->input; ?>
				</div>
				<div class="col-md-2">
					<span id="uncheckalltable" class="btn btn-danger">取消選取所有</span>
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

<script type="text/javascript">
	// Initialize RouteJs
	jQuery(document).ready(function()
	{
		RouteJs.initialize();
	});
</script>
