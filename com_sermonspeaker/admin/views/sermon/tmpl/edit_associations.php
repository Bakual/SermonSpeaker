<?php
/**
 * Copied from com_content, article view
 */

defined('_JEXEC') or die;

$fields = $this->form->getFieldset('item_associations');
?>
<fieldset>
	<?php foreach ($fields as $field) : ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo $field->label ?>
			</div>
			<div class="controls">
				<?php echo $field->input; ?>
			</div>
		</div>
	<?php endforeach; ?>
</fieldset>
