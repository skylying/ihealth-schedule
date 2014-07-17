<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Data\Data;

// No direct access
defined('_JEXEC') or die;

// Prepare script
JHtmlBehavior::multiselect('adminForm');

/**
 * Prepare data for this template.
 *
 * @var $container Windwalker\DI\Container
 * @var $asset	   Windwalker\Helper\AssetHelper
 * @var $data      Windwalker\Data\Data
 * @var $date      \JDate
 */
$container = $this->getContainer();
$asset     = $container->get('helper.asset');
$grid      = $data->grid;
$date      = $container->get('date');

// Include external js
$asset->addJS('holidays/full-calendar.js');

// Prepare data
$year = $this->data->currentYear;
$offDays = $this->data->offDays;

?>

<script>
	// Initialize javascript
	jQuery(document).ready(function()
	{
		window.fullCalendarJs.initialize();
	})
</script>

<div class="wrapper">
	<div class="calendar-body">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<h1 class="center">
					<span id="yeartitle"><?php echo $year; ?></span> 年度
				</h1>
			</div>
			<div class="col-md-4 center">
				<!--TODO: 之後視情況看要不要加上這個功能-->
				<!--<span id="previous" class="btn btn-info">
					<span class="glyphicon glyphicon-chevron-left"></span> 切換所有週六
				</span>
				<span id="next" class="btn btn-info">
					切換所有週日<span class="glyphicon glyphicon-chevron-right"></span>
				</span>-->
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<h3>一月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 1, $offDays); ?>
			</div>
			<div class="col-md-4">
				<h3>二月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 2, $offDays); ?>
			</div>
			<div class="col-md-4">
				<h3>三月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 3, $offDays); ?>
			</div>
		</div>
		<br />
		<div class="row">
			<div class="col-md-4">
				<h3>四月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 4, $offDays); ?>
			</div>
			<div class="col-md-4">
				<h3>五月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 5, $offDays); ?>
			</div>
			<div class="col-md-4">
				<h3>六月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 6, $offDays); ?>
			</div>
		</div>
		<br />
		<div class="row">
			<div class="col-md-4">
				<h3>七月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 7, $offDays); ?>
			</div>
			<div class="col-md-4">
				<h3>八月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 8, $offDays); ?>
			</div>
			<div class="col-md-4">
				<h3>九月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 9, $offDays); ?>
			</div>
		</div>
		<br />
		<div class="row">
			<div class="col-md-4">
				<h3>十月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 10, $offDays); ?>
			</div>
			<div class="col-md-4">
				<h3>十一月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 11, $offDays); ?>
			</div>
			<div class="col-md-4">
				<h3>十二月份</h3>
				<?php echo Schedule\Helper\CalendarHelper::getCalendar($year, 12, $offDays); ?>
			</div>
		</div>
	</div>

	<div id="hidden-inputs-area" class="hide">

	</div>
</div>
