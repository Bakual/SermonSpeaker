<?php
// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$uri = JURI::getInstance();
$self = $uri->toString();
?>
<script type="text/javascript">
	function submitbutton(task)
	{
		if (task == 'sermon.cancel' || document.formvalidator.isValid(document.id('sermon-form'))) {
			<?php echo $this->form->getField('notes')->save(); ?>
			submitform(task);
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="sermon-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_SERMONSPEAKER_NEW_SERMON') : JText::sprintf('COM_SERMONSPEAKER_EDIT_SERMON', $this->item->id); ?></legend>
			<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('sermon_title'); ?>
			<?php echo $this->form->getInput('sermon_title'); ?></li>

			<li><?php echo $this->form->getLabel('alias'); ?>
			<?php echo $this->form->getInput('alias'); ?></li>

			<li><?php echo $this->form->getLabel('state'); ?>
			<?php echo $this->form->getInput('state'); ?></li>

			<li><?php echo $this->form->getLabel('podcast'); ?>
			<?php echo $this->form->getInput('podcast'); ?></li>

			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>
			</ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_FIELD_AUDIOFILE_LABEL'); ?></legend>
			<input type="radio" name="sel1" value="0" onclick="enableElement(this.form.elements['jform_audiofile_text'], this.form.elements['jform_audiofile']);" checked>
			<input name="jform[audiofile]" id="jform_audiofile_text" value="<?php echo $this->form->getValue('audiofile'); ?>" class="inputbox" size="100" type="text">
			<div class="clr"></div>
			<?php echo $this->form->getLabel(''); ?>
			<input type="radio" name="sel1" value="1" onclick="enableElement(this.form.elements['jform_audiofile'], this.form.elements['jform_audiofile_text']);">
			<?php echo $this->form->getInput('audiofile'); ?>
			<img class="pointer" onClick="window.location.href='<?php echo $self; ?>&amp;type=audio&amp;file=<?php echo '/'.$this->params->get('path').'/'; ?>'+document.adminForm.jform_audiofile.value;" src='<?php echo JURI::root(); ?>/media/com_sermonspeaker/images/find.png' alt='lookup ID3' title='lookup ID3'>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_FIELD_VIDEOFILE_LABEL'); ?></legend>
			<input type="radio" name="sel2" value="0" onclick="enableElement(this.form.elements['jform_videofile_text'], this.form.elements['jform_videofile']);" checked>
			<input name="jform[videofile]" id="jform_videofile_text" value="<?php echo $this->form->getValue('videofile'); ?>" class="inputbox" size="100" type="text">
			<div class="clr"></div>
			<?php echo $this->form->getLabel(''); ?>
			<input type="radio" name="sel2" value="1" onclick="enableElement(this.form.elements['jform_videofile'], this.form.elements['jform_videofile_text']);">
			<?php echo $this->form->getInput('videofile'); ?>
			<img class="pointer" onClick="window.location.href='<?php echo $self; ?>&amp;type=video&amp;file=<?php echo '/'.$this->params->get('path').'/'; ?>'+document.adminForm.jform_videofile.value;" src='<?php echo JURI::root(); ?>/media/com_sermonspeaker/images/find.png' alt='lookup ID3' title='lookup ID3'>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL'); ?></legend>
			<div class="clr"></div>
			<?php echo $this->form->getInput('notes'); ?>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_FIELD_ADDFILE_LABEL'); ?></legend>
			<input type="radio" name="sel3" value="0" onclick="enableElement(this.form.elements['jform_addfile_text'], this.form.elements['jform_addfile']);" checked>
			<input name="jform[addfile]" id="jform_addfile_text" value="<?php echo $this->form->getValue('addfile'); ?>" class="inputbox" size="100" type="text">
			<div class="clr"></div>
			<input type="radio" name="sel3" value="1" onclick="enableElement(this.form.elements['jform_addfile'], this.form.elements['jform_addfile_text']);">
			<?php echo $this->form->getInput('addfile'); ?>
			<ul>
			<li><?php echo $this->form->getLabel('addfileDesc'); ?>
			<?php echo $this->form->getInput('addfileDesc'); ?></li>
			</ul>
		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<fieldset class="adminform" style="border: 1px dashed silver; padding: 5px; margin: 18px 0px 10px;">
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('info') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>
		<?php echo JHtml::_('sliders.start','sermon-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
		<?php echo JHtml::_('sliders.panel',JText::_('COM_SERMONSPEAKER_DETAIL'), 'detail-panel'); ?>
		<fieldset class="panelform">
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('detail') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

		<?php if ($this->params->get('custom1') OR $this->params->get('custom2')):
			echo JHtml::_('sliders.panel',JText::_('COM_SERMONSPEAKER_FIELDSET_CUSTOM_LABEL'), 'custom-panel'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('custom') as $field): ?>
					<li>
						<?php if (!$field->hidden): ?>
							<?php echo $field->label; ?>
						<?php endif; ?>
						<?php echo $field->input; ?>
					</li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		<?php endif; ?>

		<?php echo JHtml::_('sliders.panel',JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-panel'); ?>
		<fieldset class="panelform">
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('metadata') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<div class="clr"></div>
</form>