<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\View\Layout\FileLayout;
use Schedule\Script\AddressScript;
use Windwalker\Data\Data;

// No direct access
defined('_JEXEC') or die;

// Prepare script
JHtmlBootstrap::tooltip();
JHtmlFormbehavior::chosen('select');

/**
 * Prepare data for this template.
 *
 * @var Windwalker\DI\Container       $container
 * @var Windwalker\Helper\AssetHelper $asset
 * @var Windwalker\Data\Data          $data
 */
$container = $this->getContainer();
$asset     = $container->get('helper.asset');

AddressScript::bind('filter_schedule_city', 'filter_schedule_area');

$asset->addJS('schedules/list.js');
$asset->internalCSS('
div.modal.hide
{
	overflow: visible;
	top:300px;
}
div.modal.hide.in
{
	top:0px !important;
}
.status-dropdown-menu > .dropdown-menu
{
	z-index: 1031;
}
');

$editFormFields = $data->editFormFields;
?>

<?php echo $data->notifyMessage; ?>

<div id="schedule" class="windwalker schedules tablelist row-fluid">
	<form action="<?php echo JUri::getInstance(); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty($this->data->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<h4 class="page-header"><?php echo JText::_('JOPTION_MENUS'); ?></h4>
			<?php echo $this->data->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else: ?>
		<div id="j-main-container">
	<?php endif;?>

			<?php echo with(new FileLayout('joomla.searchtools.default'))->render(array('view' => $this->data)); ?>

			<?php echo $this->loadTemplate('table'); ?>

			<?php echo with(new FileLayout('joomla.batchtools.modal'))->render(array('view' => $this->data, 'task_prefix' => 'schedules.')); ?>

			<!-- Hidden Inputs -->
			<div id="hidden-inputs">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="status" value="" />
				<input type="hidden" name="new_date" value="" />
				<input type="hidden" name="new_sender_id" value="" />
				<input type="hidden" name="notify_schedule_cid" value="" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</form>
</div>

<!-- Add New Modal -->
<div id="modal-add-new-item" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-body" style="overflow: hidden;">
		<iframe src="" frameborder="0" width="100%" height="531"></iframe>
	</div>
</div>

<!-- Edit Modal -->
<div id="modal-edit-item" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 class="modal-title">排程調整</h3>
	</div>
	<div class="modal-body">
		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label">
					外送日期
				</label>
				<div class="controls">
					<?php echo $editFormFields['date']; ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					外送藥師
				</label>
				<div class="controls">
					<?php echo $editFormFields['sender_id']; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button id="modal-edit-item-submit" type="button" class="btn btn-primary">儲存</button>
	</div>
</div>

<!-- Update Status Modal (for cancel_reject and cancel_only) -->
<div id="modal-status-cancel" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 class="modal-title">取消排程</h3>
	</div>
	<div class="modal-body">
		<form action="<?php echo JUri::getInstance(); ?>" method="post" id="form-status-cancel" style="margin:0;">
			<div class="form-horizontal">
				<div class="control-group">
					<label class="control-label">
						取消原因
					</label>
					<div class="controls">
						<div class="radio">
							<input type="radio" id="status-cancel-reason-1" name="cancel" value="badservice">
							<label for="status-cancel-reason-1">服務不周</label>
						</div>

						<div class="radio">
							<input type="radio" id="status-cancel-reason-2" name="cancel" value="changedrug">
							<label for="status-cancel-reason-2">醫師換藥</label>
						</div>

						<div class="radio">
							<input type="radio" id="status-cancel-reason-3" name="cancel" value="passaway">
							<label for="status-cancel-reason-3">往生</label>
						</div>

						<div class="radio">
							<input type="radio" id="status-cancel-reason-4" name="cancel" value="other">
							<label for="status-cancel-reason-4">其他</label>
						</div>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="status-cancel-note">
						備註
					</label>
					<div class="controls">
						<textarea id="status-cancel-note" name="cancel_note" class="form-control"></textarea>
					</div>
				</div>

				<!-- Hidden Inputs -->
				<div class="hide">
					<input type="hidden" name="task" value="schedules.update.status" />
					<input type="hidden" name="cid[]" value="" />
					<input type="hidden" name="boxchecked" value="1" />
					<input type="hidden" name="status" value="" />
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-danger" onclick="jQuery('#form-status-cancel').submit();">取消排程</button>
	</div>
</div>

<!-- Update Status Modal (for pause) -->
<div id="modal-status-pause" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 class="modal-title">暫緩排程</h3>
	</div>
	<div class="modal-body">
		<form action="<?php echo JUri::getInstance(); ?>" method="post" id="form-status-pause" style="margin:0;">
			<div class="form-horizontal">
				<div class="control-group">
					<label class="control-label">
						暫緩原因
					</label>
					<div class="controls">
						<div class="radio">
							<input type="radio" id="pause-status-cancel-reason-1" name="cancel" value="hospitalized">
							<label for="pause-status-cancel-reason-1">住院</label>
						</div>
						<div class="radio">
							<input type="radio" id="pause-status-cancel-reason-2" name="cancel" value="other">
							<label for="pause-status-cancel-reason-2">其他</label>
						</div>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="pause-status-cancel-note">
						備註
					</label>
					<div class="controls">
						<textarea id="pause-status-cancel-note" name="cancel_note" class="form-control"></textarea>
					</div>
				</div>

				<!-- Hidden Inputs -->
				<div class="hide">
					<input type="hidden" name="task" value="schedules.update.status" />
					<input type="hidden" name="cid[]" value="" />
					<input type="hidden" name="boxchecked" value="1" />
					<input type="hidden" name="status" value="pause" />
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-primary" onclick="jQuery('#form-status-pause').submit();">暫緩排程</button>
	</div>
</div>

<!-- 打包表 Filter Modal -->
<div id="modal-sorted-preview" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 class="modal-title">請選擇外送日與藥師</h3>
	</div>
	<div class="modal-body">
		<?php
		echo (new FileLayout('schedule.drugdetail.filter'))->render(
			new Data(['form' => $this->data->drugDetailForm, 'formId' => 'sorted-preview-form'])
		);
		?>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-primary" onclick="jQuery('#sorted-preview-form').submit();">送出</button>
	</div>
</div>

<!-- 列印排程統計報表 Filter Modal -->
<div id="modal-report-print" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 class="modal-title">處方統計表</h3>
	</div>
	<div class="modal-body row-fluid">
		<?php
		echo (new FileLayout('schedule.schedules.report_form'))->render(
			new Data(['printForm' => $this->data->printForm, 'formId' => 'print-schedule-report-form'])
		);
		?>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-primary" onclick="jQuery('#print-schedule-report-form').submit();">送出</button>
	</div>
</div>
