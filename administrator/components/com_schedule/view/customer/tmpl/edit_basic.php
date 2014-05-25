<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use \Schedule\Helper\AddressHelper;
use \Schedule\Helper\AddressBlockHelper;

// Add css style
$doc = JFactory::getDocument();
$css = <<<CSS
.visibleinput
{
	display: block;
	border-radius: 5px;
	padding: 8px;
}
.visibleinput .number
{
	margin-bottom:0px;
}
.visibleinput span
{
	color: white;
	padding: 4px;
	border-radius: 5px;
	background: #ddd;
}
.default
{
	background: #53E253 !important;
}
.visibleinput span:hover
{
	background: #53E253;
	cursor: pointer;
}
CSS;

$doc->addStyleDeclaration($css);

// Incluce all js external file
$data->asset->addJS('multi-row-handler.js');
$data->asset->addJS('customer/customer.js');

// Prepare all field we need
$fieldsets    = $data->form->getFieldsets();
$typeField    = $data->form->getField('type');
$officePhones = $data->form->getField('tel_office');
$homePhones   = $data->form->getField('tel_home');
$mobilePhones = $data->form->getField('mobile');

// Get customer type
$customerType = $data->item->type;

// Get hidden address value
$addresses = json_decode($data->form->getValue('address'));

?>


<div class="row-fluid">
	<div class="span6">
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['edit'], 'class' => 'form-horizontal')); ?>
	</div>

	<div class="span6">
		<?php echo $typeField->input; ?>
		<div id="individualdiv" class="<?php echo $customerType == 'individual' ? '' : 'hide'; ?>">
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['rxindividual'], 'class' => 'form-horizontal')); ?>


			<div class="row">
				<fieldset>
					<legend>宅配地址</legend>
					<!--Hidden address input field-->
					<?php echo $data->form->getControlGroup('address'); ?>
					<?php echo $data->form->getControlGroup('city'); ?>
					<?php echo $data->form->getControlGroup('area'); ?>

					<div id="address-append-area" class="container-fluid">
						<?php

						// Print all addresses
						foreach ($addresses as $value)
						{
							$city = $value->city;
							$area = $value->area;
							$address = $value->address;
							$previous = $value->previous;

							echo AddressBlockHelper::getAddressBlock($city, $area, $address, $previous);
						}

						?>
					</div>
					<div id="newaddress" class="btn btn-info pull-right">
						<span class="icon-plus icon-white"></span>
						新增地址
					</div>
				</fieldset>
			</div>


			<div class="row">
				<?php echo $officePhones->input;?>
				<?php echo $homePhones->input;?>
				<?php echo $mobilePhones->input;?>
			</div>
		</div>
		<div id="residentdiv" class="<?php echo $customerType == 'resident' ? '' : 'hide'; ?>">
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['institute'], 'class' => 'form-horizontal')); ?>
		</div>
	</div>
</div>


<!--Hidden address block template-->
<script id="address-template" class="hide" type="text/html">
	<div class="row address-row" style="margin-bottom:20px;">
		<div class="col-md-1 visibleinput">
			<span class="glyphicon glyphicon-ok"></span>
		</div>
		<div class="col-md-9">
			<div class="row">
				<div class="col-md-6 citydiv"><?php echo AddressHelper::getCityList('city', array('class' => 'form-control citylist')); ?></div>
				<div class="col-md-6 areadiv"><?php echo AddressHelper::getAreaList(1, 'area', array('class' => 'form-control arealist')); ?></div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<input type="text" class="form-control roadname" style="margin-top:10px;"/>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<button type="button" class="btn btn-danger deleteaddress">
				<span class="glyphicon glyphicon-trash"></span>
			</button>
		</div>
	</div>
</script>
<!--Hidden address block template-->

<script type="text/javascript">

jQuery(document).ready(function()
{
	CustomerJs.initialize();
});

</script>
