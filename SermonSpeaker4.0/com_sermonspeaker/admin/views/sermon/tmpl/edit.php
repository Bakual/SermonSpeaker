<?php
// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$uri = JURI::getInstance();
$uri->delVar('file');
$uri->delVar('type');
$self = $uri->toString();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'sermon.cancel' || navigator.appName == 'Microsoft Internet Explorer' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('notes')->save(); ?>
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

</script>


<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_SERMONSPEAKER_NEW_SERMON') : JText::sprintf('COM_SERMONSPEAKER_EDIT_SERMON', $this->item->id); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('sermon_title'); ?>
				<?php echo $this->form->getInput('sermon_title'); ?></li>

				<li><?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?></li>

				<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?></li>

				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>

				<li><?php echo $this->form->getLabel('podcast'); ?>
				<?php echo $this->form->getInput('podcast'); ?></li>

				<li><?php echo $this->form->getLabel('ordering'); ?>
				<?php echo $this->form->getInput('ordering'); ?></li>

				<li><?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?></li>
			</ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_FIELD_AUDIOFILE_LABEL'); ?></legend>
			<input type="radio" name="sel1" value="0" onclick="enableElement(this.form.elements['jform_audiofile_text'], this.form.elements['jform_audiofile']);" checked>
			<input name="jform[audiofile]" id="jform_audiofile_text" value="<?php echo $this->form->getValue('audiofile'); ?>" class="inputbox" size="100" type="text">
			<img class="pointer" onclick="lookup(document.adminForm.jform_audiofile_text);" src='<?php echo JURI::root(); ?>/media/com_sermonspeaker/icons/16/glasses.png' alt="lookup ID3" title="lookup ID3">
			<div class="clr"></div>
			<?php echo $this->form->getLabel(''); ?>
			<input type="radio" name="sel1" value="1" onclick="enableElement(this.form.elements['jform_audiofile'], this.form.elements['jform_audiofile_text']);">
			<?php echo $this->form->getInput('audiofile');
			if (!$this->params->get('path_mode_audio', 0)) { ?>
				<img class="pointer" onclick="lookup(document.adminForm.jform_audiofile);" src='<?php echo JURI::root(); ?>/media/com_sermonspeaker/icons/16/glasses.png' alt='lookup ID3' title='lookup ID3'>
			<?php } ?>
			<div id="infoUpload1" class="intend">
				<span id="btnUpload1"></span>
				<button id="btnCancel1" type="button" onclick="cancelQueue(upload1);" class="ss-hide upload_button" disabled="disabled">Cancel</button>
				<span id="audiopathinfo" class="pathinfo ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
					<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO');
					if ($this->s3audio):
						echo ' http://'.$this->params->get('s3_bucket', '').'.s3.amazonaws.com/';
					else:
						echo ' /'.trim($this->params->get('path'), '/').'/<span id="audiopathdate" class="pathdate">'.$this->append_date.'</span><span id="audiopathlang" class="pathlang">'.$this->append_lang.'</span>';
					endif; ?>
				</span>
			</div>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_FIELD_VIDEOFILE_LABEL'); ?></legend>
			<input type="radio" name="sel2" value="0" onclick="enableElement(this.form.elements['jform_videofile_text'], this.form.elements['jform_videofile']);" checked>
			<input name="jform[videofile]" id="jform_videofile_text" value="<?php echo $this->form->getValue('videofile'); ?>" class="inputbox" size="100" type="text">
			<img class="pointer" onclick="lookup(document.adminForm.jform_videofile_text);" src='<?php echo JURI::root(); ?>/media/com_sermonspeaker/icons/16/glasses.png' alt="lookup ID3" title="lookup ID3">
			<div class="clr"></div>
			<?php echo $this->form->getLabel(''); ?>
			<input type="radio" name="sel2" value="1" onclick="enableElement(this.form.elements['jform_videofile'], this.form.elements['jform_videofile_text']);">
			<?php echo $this->form->getInput('videofile'); 
			if ($this->params->get('path_mode_video', 0) < 2) { ?>
				<img class="pointer" onclick="lookup(document.adminForm.jform_videofile);" src='<?php echo JURI::root(); ?>/media/com_sermonspeaker/icons/16/glasses.png' alt='lookup ID3' title='lookup ID3'>
			<?php } ?>
			<div id="infoUpload2" class="intend">
				<span id="btnUpload2"></span>
				<button id="btnCancel2" type="button" onclick="cancelQueue(upload2);" class="ss-hide upload_button" disabled="disabled">Cancel</button>
				<span id="videopathinfo" class="pathinfo ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
					<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO');
					if ($this->s3video):
						echo ' http://'.$this->params->get('s3_bucket', '').'.s3.amazonaws.com/';
					else:
						echo ' /'.trim($this->params->get('path'), '/').'/<span id="videopathdate" class="pathdate">'.$this->append_date.'</span><span id="videopathlang" class="pathlang">'.$this->append_lang.'</span>';
					endif; ?>
				</span>
			</div>
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
			<div id="infoUpload3" class="intend">
				<span id="btnUpload3"></span>
				<button id="btnCancel3" type="button" onclick="cancelQueue(upload3);" class="ss-hide upload_button" disabled="disabled">Cancel</button>
				<span id="addfilepathinfo" class="pathinfo ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
					<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO').' /'.trim($this->params->get('path_addfile'), '/').'/<span id="addfilepathdate" class="pathdate">'.$this->append_date.'</span><span id="addfilepathlang" class="pathlang">'.$this->append_lang.'</span>'; ?>
				</span>
			</div>
			<ul>
			<li><?php echo $this->form->getLabel('addfileDesc'); ?>
			<?php echo $this->form->getInput('addfileDesc'); ?></li>
			</ul>
		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<fieldset class="adminform" style="border: 1px dashed silver; padding: 5px; margin: 18px 0px 10px;">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>

				<li><?php echo $this->form->getLabel('created_by'); ?>
				<?php echo $this->form->getInput('created_by'); ?></li>

				<li><?php echo $this->form->getLabel('created'); ?>
				<?php echo $this->form->getInput('created'); ?></li>

				<?php if ($this->item->modified_by) : ?>
					<li><?php echo $this->form->getLabel('modified_by'); ?>
					<?php echo $this->form->getInput('modified_by'); ?></li>

					<li><?php echo $this->form->getLabel('modified'); ?>
					<?php echo $this->form->getInput('modified'); ?></li>
				<?php endif; ?>

				<li><?php echo $this->form->getLabel('hits'); ?>
				<?php echo $this->form->getInput('hits'); ?></li>
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