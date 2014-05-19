<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JHtmlBootstrap::tooltip();
JHtmlFormbehavior::chosen('select');
JHtmlBehavior::formvalidation();

Schedule\Script\AddressScript::bind('jform_city', 'jform_area');

/**
 * Prepare data for this template.
 *
 * @var $data       Windwalker\Data\Data
 * @var $container  Windwalker\DI\Container
 * @var $fieldSet   JFormField[]
 * @var $asset      Windwalker\Helper\AssetHelper
 */
$container = $this->getContainer();
$fieldSet  = $data->form->getFieldset('information');
$asset     = $container->get('helper.asset');
$isEdit    = ($data->item->id > 0);
$jsOptions = array('isEdit' => $isEdit);

$uneditableFields = array('type', 'institute_id', 'city', 'area');

$asset->addJS('route/edit.js');
?>
<!-- Validate Script -->
<script type="text/javascript">
	jQuery(function($)
	{
		RouteEdit.run(<?php echo json_encode($jsOptions); ?>);
	});

	Joomla.submitbutton = function(task)
	{
		if (task == 'route.edit.cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	};
</script>

<div id="schedule" class="windwalker route edit-form row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>"  method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data">
		<div class="row-fluid">
			<div class="span8">
				<fieldset class="form-horizontal">
					<legend>路線資訊</legend>
					<?php foreach ($fieldSet as $field): ?>
						<?php
						if ($isEdit && in_array($field->fieldname, $uneditableFields))
						{
							$field->readonly = true;
						}
						?>
						<div id="control_<?php echo $field->id; ?>">
							<?php echo $field->getControlGroup() . "\n\n"; ?>
						</div>
					<?php endforeach;?>
				</fieldset>
			</div>
		</div>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="com_schedule" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="cid" value="<?php echo implode(',', $data->cid); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
