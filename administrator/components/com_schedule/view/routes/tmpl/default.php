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
}
.route-outer div
{
	padding: 0 5px;
}
.route-outer div label
{
	display:inline-block;
	padding-top:5px;

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
					<button class="btn btn-success">預覽結果</button>
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
	(function($)
	{
		$('#routeupdater_sender_id').on('change', function()
		{
			alert('媽我在這裡 ._./~');
		})
	})(jQuery)
</script>