<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

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

<form action="<?php JRoute::_('index.php?option=com_sermonspeaker'); ?>" method="post" name="adminForm" id="sermon-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_SERMONSPEAKER_NEW_SERMON') : JText::sprintf('COM_SERMONSPEAKER_EDIT_SERMON', $this->item->id); ?></legend>
			<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('sermon_title'); ?>
			<?php echo $this->form->getInput('sermon_title'); ?></li>

			<li><?php echo $this->form->getLabel('alias'); ?>
			<?php echo $this->form->getInput('alias'); ?></li>

			<li><?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?></li>

			<li><?php echo $this->form->getLabel('podcast'); ?>
			<?php echo $this->form->getInput('podcast'); ?></li>

			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>
			</ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_SERMONPATH'); ?></legend>
			<input type="radio" name="sel" value="1" onclick="enableElement(this.form.elements['jform_sermon_path'], this.form.elements['jform_sermon_path_choice']);" checked>
			<?php echo $this->form->getInput('sermon_path'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getLabel(''); ?>
			<input type="radio" name="sel" value="2" onclick="enableElement(this.form.elements['jform_sermon_path_choice'], this.form.elements['jform_sermon_path']);">
			<?php echo $this->sermon_files; ?>
			<img onClick="window.location.href='<?php echo $self; ?>&amp;file='+document.adminForm.jform_sermon_path_choice.value;" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/find.png' alt='lookup ID3' title='lookup ID3'>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?></legend>
			<div class="clr"></div>
			<?php echo $this->form->getInput('notes'); ?>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></legend>
			<input type="radio" name="jform[seladdfile]" value="1" onclick="enableElement(this.form.elements['jform_addfile'], this.form.elements['jform_addfile_choice']);" checked>
			<?php echo $this->form->getInput('addfile'); ?>
			<div class="clr"></div>
			<input type="radio" name="jform[seladdfile]" value="2" onclick="enableElement(this.form.elements['jform_addfile_choice'], this.form.elements['jform_addfile']);">
			<?php echo $this->addfiles; ?>
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
		<?php echo JHtml::_('sliders.panel',JText::_('COM_SERMONSPEAKER_GENERAL'), 'general-panel'); ?>
		<fieldset class="panelform">
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('general') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

		<?php echo JHtml::_('sliders.panel',JText::_('COM_SERMONSPEAKER_CUSTOM'), 'custom-panel'); ?>
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