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

// Get current year and month
$year = date("Y");
$month = (int) date("m");

?>

<div class="container">
	<div class="calendar-body">
		<div class="row">
			<div class="col-md-3 center">
				<span id="previous" class="btn btn-info">
					<span class="glyphicon glyphicon-chevron-left"></span>上個月
				</span>
			</div>
			<div class="col-md-6">
				<h1 class="center">
					<span id="yeartitle"><?php echo $year; ?></span> 年
					<span id="monthtitle"><?php echo $month; ?></span> 月
				</h1>
			</div>
			<div class="col-md-3 center">
				<span id="next" class="btn btn-info">
					下個月<span class="glyphicon glyphicon-chevron-right"></span>
				</span>
			</div>
		</div>
		<div class="row">
			<?php //echo Schedule\Helper\CalendarHelper::getCalendar($year, $month, '', 800); ?>
		</div>
	</div>
</div>

<script>

	;(function($)
	{
		window.ajaxCalendarJs = {

			initialize : function()
			{
				var date = new Date;

				this.calendar    = $('#calendar');
				this.previous    = $('#previous');
				this.next        = $('#next');
				this.yearTitle   = $('#yeartitle');
				this.monthTitle  = $('#monthtitle');

				// Get current year
				this.currentYear  = date.getFullYear();
				this.currentMonth = date.getMonth() + 1;

				// Global namespace for "this"
				$this = this;

				// Bind all event we need
				$this.bindEvent();
			},

			bindEvent : function()
			{
				// Bind next button
				$this.next.on('click', function(e)
				{
					// Calculate target month
					var targetDate = {};
					targetDate.year  = ($this.currentMonth == 12) ? $this.currentYear + 1 : $this.currentYear;
					targetDate.month = ($this.currentMonth == 12) ? 1 : $this.currentMonth + 1;

					$this.fireAjax(targetDate);
				});

				// Bind previous button
				$this.previous.on('click', function(e)
				{
					// Calculate target month
					var targetDate = {};
					targetDate.year  = ($this.currentMonth == 1) ? $this.currentYear - 1 : $this.currentYear;
					targetDate.month = ($this.currentMonth == 1) ? 12 : $this.currentMonth - 1;

					$this.fireAjax(targetDate);
				});

				$this.calendar.on('click', 'td', function()
				{
					$(this).css('background', 'pink');
				});
			},

			/**
			 * Ajax request to get next or previous calendar
			 *
			 * @param {object} targetDate
			 */
			fireAjax : function(targetDate)
			{
				var ajaxUrl = window.location.pathname + '?option=com_schedule&task=holidays.calendar.json';

				$.ajax({
					type : "get",
					url : ajaxUrl,
					data : targetDate,
					success : function(data)
					{
						var obj = $.parseJSON(data);

						$this.calendar.html(obj.calendarhtml);

						// update current year and month
						$this.currentYear = parseInt(obj.year);
						$this.currentMonth = parseInt(obj.month);

						// update calendar title
						$this.yearTitle.text($this.currentYear);
						$this.monthTitle.text($this.currentMonth);
					}
				})
			}
		}
	})(jQuery);




	jQuery(document).ready(function()
	{
		window.ajaxCalendarJs.initialize();

	})

</script>
