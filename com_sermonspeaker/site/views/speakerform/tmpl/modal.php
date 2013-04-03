<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::stylesheet('com_sermonspeaker/frontendupload.css', '', true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'speakerform.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('intro')->save(); ?>
			<?php echo $this->form->getField('bio')->save(); ?>
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<div class="edit<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=speakerform&modal=1&s_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<fieldset>
		<legend><?php echo JText::_('JEDITOR'); ?></legend>
		<div class="formelm">
			<?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('alias'); ?>
			<?php echo $this->form->getInput('alias'); ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('catid'); ?>
			<?php echo $this->form->getInput('catid'); ?>
		</div>
		<?php if ($this->user->authorise('core.edit.state', 'com_sermonspeaker')): ?>
			<div class="formelm">
				<?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?>
			</div>
		<?php endif; ?>
		<div class="formelm-buttons">
			<button type="button" onclick="Joomla.submitbutton('speakerform.save');">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="window.parent.SqueezeBox.close();">
				<?php echo JText::_('JCANCEL') ?>
			</button>
		</div>
		<div>
			<?php echo $this->form->getLabel('intro'); ?>
			<?php echo $this->form->getInput('intro'); ?>
		</div>
		<div style="clear:both"><br /></div>
		<div>
			<?php echo $this->form->getLabel('bio'); ?>
			<?php echo $this->form->getInput('bio'); ?>
		</div>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('JDETAILS'); ?></legend>
		<?php foreach($this->form->getFieldset('detail') as $field): ?>
			<div class="formelm">
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
				<?php if ($field->fieldname == 'pic'): ?>
					<div style="clear:both"></div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<div class="formelm-buttons">
			<button type="button" onclick="Joomla.submitbutton('speakerform.save');">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="window.parent.SqueezeBox.close();">
				<?php echo JText::_('JCANCEL') ?>
			</button>
		</div>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?></legend>
		<div class="formelm-area">
		<?php echo $this->form->getLabel('language'); ?>
		<?php echo $this->form->getInput('language'); ?>
		</div>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_SERMONSPEAKER_METADATA'); ?></legend>
		<?php foreach($this->form->getFieldset('metadata') as $field): ?>
			<div class="formelm">
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</div>
		<?php endforeach; ?>
	</fieldset>
	<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>