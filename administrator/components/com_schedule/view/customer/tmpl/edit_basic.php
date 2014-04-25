<?php
/**
 * Part of Component Schedule files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$tab       = $data->tab;
$fieldsets = $data->form->getFieldsets();
?>

<div class="row-fluid">
	<div class="span6">
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['edit'], 'class' => 'form-horizontal')); ?>
	</div>

	<div class="span6">
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['contact'], 'class' => 'form-horizontal')); ?>
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['address'], 'class' => 'form-horizontal')); ?>
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['phones'], 'class' => 'form-horizontal')); ?>
	</div>
</div>
