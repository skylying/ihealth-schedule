<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Schedule\Helper\ScheduleHelper;
use Schedule\Helper\ApiReturnCodeHelper;
use Schedule\Helper\AddressHelper;
use \Windwalker\DI\Container;

JHtmlBootstrap::tooltip();
JHtmlFormbehavior::chosen('select');
JHtmlBehavior::formvalidation();

$basic = $data->form->getFieldset("basic");
$ps    = $data->form->getFieldset("schedules_ps");

$customerId = $data->form->getField('customer_id')->id;
$customerIdNumber = $data->form->getField('id_number')->id;

$memberId = $data->form->getField('member_id')->id;

$telOfficeId = $data->form->getField('tel_office')->id;
$telHomeId = $data->form->getField('tel_home')->id;
$mobileId = $data->form->getField('mobile')->id;
$isSaveAndPrint = $data->print;
$id = $data->item->id;

$telOfficeName = $data->form->getField('tel_office')->name;
$telHomeName   = $data->form->getField('tel_home')->name;
$mobileName    = $data->form->getField('mobile')->name;

// 身分證字號
$customerIdNumber = $data->form->getField('id_number')->id;


$options = array(
	'customerId'           => $customerId,
	'customerIdNumber'     => $customerIdNumber,
	'telOfficeId'          => $telOfficeId,
	'telHomeId'            => $telHomeId,
	'mobileId'             => $mobileId,
	'seeDrDateId'          => $data->form->getField('see_dr_date')->id,
	'periodId'             => $data->form->getField('period')->id,
	'createAddressId'      => $data->form->getField('create_addresses')->id,
	'timesId'              => $data->form->getField('times')->id,
	'methodId'             => $data->form->getField('method')->id,
	'drugId'               => $data->form->getField('drug')->id,
	'deleteDrugId'         => $data->form->getField('delete_drug')->id,
	'hospitalId'           => $data->form->getField('hospital_id')->id,
	'birthDateId'          => $data->form->getField('birth_date')->id,
	'addressesKeys'        => array("1st", "2nd", "3rd"),
	'SUCCESS_ROUTE_EXIST'  => ApiReturnCodeHelper::SUCCESS_ROUTE_EXIST,
	'ERROR_NO_ROUTE'       => ApiReturnCodeHelper::ERROR_NO_ROUTE,
	'ERROR_NO_SEE_DR_DATE' => ApiReturnCodeHelper::ERROR_NO_SEE_DR_DATE
);

$data->asset->addJS('moment-with-langs.min.js');
$data->asset->addJS('method-field-handler.js');
$data->asset->addJS('deliver-schedule-handler.js');
$data->asset->addJS('customer-field-handler.js');
$data->asset->addJS('rxindividual/edit.js');
$data->asset->addJS('customer-member-field-map.js');

Schedule\Script\AddressScript::bind('jform_city', 'jform_area');

?>

<script type="text/javascript">
jQuery(document).ready(function ()
{
	RxIndividualEdit.init(<?php echo json_encode($options); ?>);
	window.customerMemberField.init('<?php echo $customerId; ?>', '<?php echo $memberId; ?>', '<?php echo JRoute::_("index.php?option=com_schedule&task=rxindividual.ajax.member", false); ?>');
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

	input.js-address-row-data
	{
		width: 100%;
	}

	.address label
	{
		display: none;
	}

	input.badge:empty
	{
		display: block !important;
	}
	input.js-tel-row-data
	{
		width: 67%;
		margin-right: 2%;
	}

	.js-nth-schedule-check .control-group
	{
		border-top: 7px solid #ccc;
		border-bottom: 8px solid #ccc;
		padding: 30px 0px;
		background-color: #F8F8F8;
	}

	.js-select-phone-default
	{
		width: 67%;
		margin-right: 2%;
	}

	.js-add-address
	{
		bottom: -30px;
		position: relative;
		width: 88%;
	}

	.save-cancel-btn-group
	{
		width: 88%;
	}

	.js-save-address, .js-cancel-address
	{
		width: 50%;
	}

	.js-tmpl-add-addressrow
	{
		padding: 10px 30px;
		margin: 0px -30px;
		background-color: rgba(255, 219, 219, .5);
	}

	.js-route-wrap
	{
		padding: 10px 30px;
		margin: 0px -30px;
		background-color: rgba(255, 219, 219, .5);
	}

	.js-alltels-wrap
	{
		border-top: 1px solid #ccc;
		padding-top: 24px;
	}

	.opaque
	{
		opacity: .3;
		transition: all .3s;
	}

	.custom-well
	{
		margin-bottom: 20px;
		background-color: #f5f5f5;
		border: 1px solid #e3e3e3;
		border-radius: 4px;
		-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
		-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
		box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
		transition: all .2s;
	}

	/** This page quick add */
	#jform_customer_id_quickadd-container
	{
		overflow: auto;
	}
	#jform_note
	{
		width: 67%;
	}
	/* Chosen Max Width */
	#jform_customer_id_chzn
	{
		max-width: 128px !important;
	}

	/* Base for label styling */
	[type="checkbox"]:not(:checked),
	[type="checkbox"]:checked {
		position: absolute;
		left: -9999px;
	}
	[type="checkbox"]:not(:checked) + label,
	[type="checkbox"]:checked + label {
		position: relative;
		padding-left: 30px;
		padding-top: 4px;
		cursor: pointer;
		font-size: 1.3em;
	}

	/* checkbox aspect */
	[type="checkbox"]:not(:checked) + label:before,
	[type="checkbox"]:checked + label:before {
		content: '';
		position: absolute;
		left:0;
		top: 2px;
		width: 22px;
		height: 22px;
		border: 1px solid #aaa;
		background: #f8f8f8;
		border-radius: 3px;
		box-shadow: inset 0 1px 3px rgba(0,0,0,.3)
	}
	/* checked mark aspect */
	[type="checkbox"]:not(:checked) + label:after,
	[type="checkbox"]:checked + label:after {
		content: '✔';
		position: absolute;
		top: 0;
		left: 4px;
		padding-top: 3px;
		font-size: 17px;
		color: #09ad7e;
		transition: all .2s;
	}
	/* checked mark aspect changes */
	[type="checkbox"]:not(:checked) + label:after {
		opacity: 0;
		transform: scale(0);
	}
	[type="checkbox"]:checked + label:after {
		opacity: 1;
		transform: scale(1);
	}
	/* disabled checkbox */
	[type="checkbox"]:disabled:not(:checked) + label:before,
	[type="checkbox"]:disabled:checked + label:before {
		box-shadow: none;
		border-color: #bbb;
		background-color: #ddd;
	}
	[type="checkbox"]:disabled:checked + label:after {
		color: #999;
	}
	[type="checkbox"]:disabled + label {
		color: #aaa;
	}
	/* accessibility */
	[type="checkbox"]:checked:focus + label:before,
	[type="checkbox"]:not(:checked):focus + label:before {
		border: 1px dotted blue;
	}


</style>
<?php if ($isSaveAndPrint == '1'): ?>
	<script>
		window.open("<?php echo JRoute::_("index.php?option=com_schedule&view=rxindividual&layout=print&tmpl=component&id=" . $id, false); ?>", "_blank");
	</script>
<?php endif; ?>

<form name="adminForm" id="adminForm" method="post" action="<?php echo JURI::getInstance(); ?>" class="form-horizontal"
	enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="col-lg-5 col-md-5 col-sm-12">
			<?php
			foreach ($basic as $field)
			{
				echo $field->getControlGroup();
			}
			?>

			<?php echo $data->form->getControlGroup('note'); ?>

		</div>
		<div class="col-lg-7 col-md-7 col-sm-12">
			<?php foreach (array("1st", "2nd", "3rd") as $key): ?>
				<?php $schedules = $data->schedulesForm->getGroup("schedules_{$key}"); ?>
				<div id="schedules_<?php echo $key; ?>" class="row-fluid schedules schedules_<?php echo $key; ?>">
					<div class="col-lg-3 js-nth-schedule-check">
						<?php echo $schedules["jform_schedules_{$key}_deliver_nth"]->getControlGroup(); ?>
					</div>
					<div class="col-lg-9 js-nth-schedule-info custom-well opaque">
						<div class="row-fluid">
							<div class="col-lg-12 ">
								<div class="js-route-wrap hide">
									<p><span class="label label-warning">宅配區域路線不存在，請指定外送藥師，外送日。</span></p>
									<div class="row-fluid">
										<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px 10px 0px 0px;">
											<?php echo $schedules["jform_schedules_{$key}_sender_id"]->getControlGroup(); ?>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 js-route-weekday" style="padding: 0px 10px 0px 0px;">
											<?php echo $schedules["jform_schedules_{$key}_weekday"]->getControlGroup(); ?>
										</div>
									</div>
								</div>
							</div>
							<!-- Add Address Row -->
							<div class="col-lg-12 js-add-address-position">

							</div>
							<div class="col-lg-12">
								<div class="row-fluid">
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 js-address-wrap" style="padding: 0px;">
										<?php echo $schedules["jform_schedules_{$key}_address_id"]->getControlGroup(); ?>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px;">
										<div class="btn btn-small btn-info pull-right js-add-address">
											<span class="icon-plus icon-white"></span>
											新增
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row-fluid">
							<div class="col-lg-4">
								<?php echo $schedules["jform_schedules_{$key}_available_receive_date"]->getControlGroup(); ?>
								<?php echo $schedules["jform_schedules_{$key}_drug_empty_date"]->getControlGroup(); ?>
							</div>
							<div class="col-lg-4">
								<?php echo $schedules["jform_schedules_{$key}_date"]->getControlGroup(); ?>
							</div>
							<div class="col-lg-4">
								<?php echo $schedules["jform_schedules_{$key}_session"]->getControlGroup(); ?>
							</div>
						</div>

						<div class="row-fluid">
							<div class="col-lg-4">
								<?php echo $schedules["jform_schedules_{$key}_tel_office"]->getControlGroup(); ?>
							</div>
							<div class="col-lg-4">
								<?php echo $schedules["jform_schedules_{$key}_tel_home"]->getControlGroup(); ?>
							</div>
							<div class="col-lg-4">
								<?php echo $schedules["jform_schedules_{$key}_mobile"]->getControlGroup(); ?>
							</div>
						</div>
					</div>
					<?php echo $schedules["jform_schedules_{$key}_schedule_id"]->getControlGroup(); ?>
				</div>
			<?php endforeach; ?>
				<div class="js-alltels-wrap">
					<div class="col-lg-12 js-tel-wrap">
						<div class="control-group">
							<div class="control-label">
								<?php echo $data->form->getLabel('tel_office'); ?>
							</div>
							<!-- This is where to put select list -->
							<div class="controls">
								<input type="text" />
							</div>
							<div class="btn btn-small btn-info pull-left js-add-tel">
								<span class="icon-plus icon-white"></span>
								新增
							</div>
							<input type="hidden" name="<?php echo $telOfficeName; ?>" id="<?php echo $telOfficeId;?>"/>
						</div>
						<!-- Add telephone row -->
						<div class="js-tmpl-add-telrow hide">
							<div class="control-group">
								<div class="controls">
									<input class="js-tel-row-data pull-left" type="text">
								</div>
								<div class="btn btn-small btn-success pull-left js-save-tel">
									<span class="icon-ok icon-white"></span>
									儲存
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12 js-tel-wrap">
						<div class="control-group">
							<div class="control-label">
								<?php echo $data->form->getLabel('tel_home'); ?>
							</div>
							<!-- This is where to put select list -->
							<div class="controls">
								<input type="text" />
							</div>
							<div class="btn btn-small btn-info pull-left js-add-tel">
								<span class="icon-plus icon-white"></span>
								新增
							</div>
							<input type="hidden" name="<?php echo $telHomeName; ?>" id="<?php echo $telHomeId;?>"/>
						</div>
						<!-- Add telephone row -->
						<div class="js-tmpl-add-telrow hide">
							<div class="control-group">
								<div class="controls">
									<input class="js-tel-row-data pull-left" type="text">
								</div>
								<div class="btn btn-small btn-success pull-left js-save-tel">
									<span class="icon-ok icon-white"></span>
									儲存
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12 js-tel-wrap">
						<div class="control-group">
							<div class="control-label">
								<?php echo $data->form->getLabel('mobile'); ?>
							</div>
							<!-- This is where to put select list -->
							<div class="controls">
								<input type="text" />
							</div>
							<div class="btn btn-small btn-info pull-left js-add-tel">
								<span class="icon-plus icon-white"></span>
								新增
							</div>
							<input type="hidden" name="<?php echo $mobileName; ?>" id="<?php echo $mobileId;?>"/>
						</div>
						<!-- Add telephone row -->
						<div class="js-tmpl-add-telrow hide">
							<div class="control-group">
								<div class="controls">
									<input class="js-tel-row-data pull-left" type="text">
								</div>
								<div class="btn btn-small btn-success pull-left js-save-tel">
									<span class="icon-plus icon-white"></span>
									儲存
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<!-- HICODE TEMPLATE
	<div class="control-group custom-well js-hicode-tmpl hide">
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="text-center">健保碼</th>
					<th class="text-center">總量</th>
					<th class="text-center">刪除</th>
					<th class="text-center">總數小計</th>
				</tr>
			</thead>

			<tfoot>
				<td colspan="3">
					<a class="btn btn-default btn-success js-hicode-add-row">
						<i class="glyphicon glyphicon-plus"></i>
						新增欄位
					</a>
				</td>
				<td colspan="1">
					<p class="text-center"><span class="js-hicode-amount" style="font-size: 2.5rem;"></span></p>
				</td>
			</tfoot>

			<tbody>
				<tr class="js-hicode-row">
					<td>
						<input class="js-hicode-code" style="width:100%;" type="text">
					</td>
					<td>
						<input class="js-hicode-quantity" style="width:100%;" type="text">
					</td>
					<td>
						<button type="button" class="btn btn-default btn-sm js-hicode-delete-row">
							<span class="glyphicon glyphicon-trash"></span>
						</button>
					</td>
					<td>
						<input class="js-hicode-id" style="width:100%;" type="hidden">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	-->

	<!-- ADD ADDRESS ROW TEMPLATE -->
	<div class="js-tmpl-add-addressrow hide">
		<div class="row-fluid">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding: 0px;">
				<div class="row-fluid">
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px 10px 0px 0px;">
						<div class="control-group">
							<div class="control-label">
								縣市
							</div>
							<!-- This is where to put select list -->
							<div class="controls">
								<?php echo $data->form->getInput('city') ?>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px 10px 0px 0px;">
						<div class="control-group">
							<div class="control-label">
								區域
							</div>
							<!-- This is where to put select list -->
							<div class="controls">
								<?php echo $data->form->getInput('area') ?>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px">
						<div class="control-group">
							<div class="control-label">
								地址
							</div>
							<!-- This is where to put select list -->
							<div class="controls">
								<input class="js-address-row-data pull-left" type="text">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px; margin-top: 27px;">
				<div class="btn-group pull-right save-cancel-btn-group">
					<div class="btn btn-small btn-danger js-cancel-address">
						<span class="icon-cancel icon-white"></span>
						取消
					</div>
					<div class="btn btn-small btn-success js-save-address">
						<span class="icon-ok icon-white"></span>
						儲存
					</div>
				</div>
			</div>
		</div>
	</div>

	<div>
		<input type="hidden" name="save-and-print" value="0"/>
		<input type="hidden" name="option" value="com_schedule" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
