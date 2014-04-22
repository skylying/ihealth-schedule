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
	<div class="span8">
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['information'], 'class' => 'form-horizontal')); ?>
	</div>
</div>