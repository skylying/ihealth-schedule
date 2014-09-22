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
JHtmlFormbehavior::chosen('select');
JHtmlDropdown::init();

/**
 * Prepare data for this template.
 *
 * @var Windwalker\DI\Container $container
 */
$container = $this->getContainer();

$doc = \JFactory::getDocument();

// ==================
// Add table style
$css = <<<CSS
td:hover
{
	cursor:pointer;
}
.off
{
	background: #FFE5E5;
	color: #9E9393;
}
CSS;

$doc->addStyleDeclaration($css);

$filters = $this->data['filterForm']->getGroup('filter');
$year = $this->data->currentYear;

?>

<div id="schedule" class="windwalker holidays tablelist row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		<?php if (!empty($this->data->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<h4 class="page-header"><?php echo JText::_('JOPTION_MENUS'); ?></h4>
			<?php echo $this->data->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php else: ?>
		<div id="j-main-container">
		<?php endif;?>

			<div class="row">
				<div class="col-md-4 col-md-offset-8">
					<?php
					foreach ($filters as $filter)
					{
						if ('filter[holiday.year]' === $filter->name)
						{
							$filter->value = $year;
						}

						echo $filter->input;
					}
					?>
				</div>
			</div>

			<br />

			<?php echo $this->loadTemplate('fullcalendar'); ?>

			<!-- Hidden Inputs -->
			<div id="hidden-inputs">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHtml::_('form.token'); ?>
			</div>

		</div>
	</form>
</div>
