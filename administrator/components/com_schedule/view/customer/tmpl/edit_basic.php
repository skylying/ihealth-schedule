<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$tab = $data->tab;
$fieldsets = $data->form->getFieldsets();
$typeField = $data->form->getField('type');
$customerType = $data->item->type;
$data->asset->addJS('moment-with-langs.min.js');
$data->asset->addJS('multi-row-handler.js');

?>
<div class="row-fluid">
	<div class="span6">
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['edit'], 'class' => 'form-horizontal')); ?>
	</div>

	<div class="span6">
		<?php echo $typeField->input; ?>
		<div id="individualdiv" class="<?php echo $customerType == 'individual' ? '' : 'hide'; ?>">
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['rxindividual'], 'class' => 'form-horizontal')); ?>
			<?php echo $data->form->getControlGroup('address'); ?>
			<div id="appendArea"></div>
			<div class="btn btn-md btn-info button-add-addr">
				<span class="icon-plus icon-white"></span>
				新增地址
			</div>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['office'], 'class' => 'form-horizontal')); ?>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['home'], 'class' => 'form-horizontal')); ?>
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['mobile'], 'class' => 'form-horizontal')); ?>
		</div>
		<div id="residentdiv" class="<?php echo $customerType == 'resident' ? '' : 'hide'; ?>">
			<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['institute'], 'class' => 'form-horizontal')); ?>
		</div>
	</div>
</div>

<div class="row-fluid js-address-row-tmpl hide">
	<div class="row-fluid alert alert-success">
		<input class="addr_id hide" type="text" />

		<div class="col-lg-2 panel-info">
			<input type="radio" name="previous" class="previous" />
			主要
		</div>
		<div class="col-lg-5">
			<?php echo $data->form->getControlGroup('city'); ?>
		</div>
		<div class="col-lg-5">
			<?php echo $data->form->getControlGroup('area'); ?>
		</div>
		<div class="col-lg-12">
			<?php echo $data->form->getControlGroup('address2'); ?>
		</div>
		<button type="button" class="btn btn-default button-delete-addr pull right">
			<span class="glyphicon glyphicon-remove"></span>
		</button>
	</div>
</div>

<script type="text/javascript">

jQuery(document).ready(function()
{
	CustomerJs.initialize();
});

</script>
