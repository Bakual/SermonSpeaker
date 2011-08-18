<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$uri = JURI::getInstance();
$uri->delVar('file0');
$uri->delVar('file1');
$uri->delVar('type');
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
<div class="ss-frup-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
	<div id="ss-frup-form">
		<form action="<?php echo JURI::root(); ?>index.php?option=com_sermonspeaker&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JUtility::getToken();?>=1" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data">
			<fieldset id="upload-noflash" class="actions">
				<legend><?php echo JText::_('COM_SERMONSPEAKER_FU_SELECTFILE'); ?></legend>
				<label for="upload-file" class="label"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_AUDIOFILE_LABEL'); ?></label>
				<input type="file" size="50" id="upload-file" name="Filedata[]" /><br />
				<label for="upload-file" class="label"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_VIDEOFILE_LABEL'); ?></label>
				<input type="file" size="50" id="upload-file" name="Filedata[]" /><br />
				<input type="submit" class="submit" value="<?php echo JText::_('COM_SERMONSPEAKER_FU_START_UPLOAD'); ?>" />
				<input type="hidden" name="return-url" value="<?php echo base64_encode($self); ?>" />
			</fieldset>
		</form>
		<form action="<?php echo JURI::root(); ?>index.php?option=com_sermonspeaker&amp;view=frontendupload&amp;task=frontendupload.save" name="fu_createsermon" id="fu_createsermon" method="post" enctype="multipart/form-data" class="form-validate">
			<?php echo $this->form->getLabel('sermon_title'); ?>
			<?php echo $this->form->getInput('sermon_title'); ?>
			<br />
			<?php echo $this->form->getLabel('alias'); ?>
			<?php echo $this->form->getInput('alias'); ?>
			<br />

			<?php echo $this->form->getLabel('audiofile'); ?>
			<input type="radio" name="sel1" value="0" onclick="enableElement(this.form.elements['jform_audiofile_text'], this.form.elements['jform_audiofile']);" checked>
				<input class="inputbox" type="text" name="jform[audiofile]" id="jform_audiofile_text" size="47" maxlength="250" value="<?php echo $this->form->getValue('audiofile'); ?>" />
					<img class="pointer" onClick="window.location.href='<?php echo JRoute::_('index.php?view=frontendupload&amp;type=audio') ;?>&amp;file0='+document.fu_createsermon.jform_audiofile_text.value+'&amp;file1='+document.fu_createsermon.jform_videofile_text.value;" src="media/com_sermonspeaker/icons/16/glasses.png" alt="lookup ID3" title="lookup ID3"><br />
			<div class="label">&nbsp;</div>
			<input type="radio" name="sel1" value="1" onclick="enableElement(this.form.elements['jform_audiofile'], this.form.elements['jform_audiofile_text']);">
				<?php echo $this->form->getInput('audiofile');
				if (!$this->params->get('path_mode_audio', 0)) {
				$path = !$this->params->get('path_mode_audio', 0) ? '/'.$this->params->get('path').'/' : ''; ?>
					<img class="pointer" onClick="window.location.href='<?php echo JRoute::_('index.php?view=frontendupload&amp;type=audio') ;?>&amp;file0=<?php echo $path; ?>'+document.fu_createsermon.jform_audiofile.value+'&amp;file1='+document.fu_createsermon.jform_videofile_text.value;" src="media/com_sermonspeaker/icons/16/glasses.png" alt="lookup ID3" title="lookup ID3">
				<?php } ?>
				<div id="infoUpload1" class="intend">
					<span id="btnUpload1"></span>
					<button id="btnCancel1" type="button" onclick="cancelQueue(upload1);" class="ss-hide" disabled="disabled">Cancel</button>
				</div>
			<div class="clr"></div>
			
			<?php echo $this->form->getLabel('videofile'); ?>
			<input type="radio" name="sel2" value="0" onclick="enableElement(this.form.elements['jform_videofile_text'], this.form.elements['jform_videofile']);" checked>
				<input class="inputbox" type="text" name="jform[videofile]" id="jform_videofile_text" size="47" maxlength="250" value="<?php echo $this->form->getValue('videofile'); ?>" />
					<img class="pointer" onClick="window.location.href='<?php echo JRoute::_('index.php?view=frontendupload&amp;type=video') ;?>&amp;file0='+document.fu_createsermon.jform_audiofile_text.value+'&amp;file1='+document.fu_createsermon.jform_videofile_text.value;" src="media/com_sermonspeaker/icons/16/glasses.png" alt="lookup ID3" title="lookup ID3"><br />
			<div class="label">&nbsp;</div>
			<input type="radio" name="sel2" value="1" onclick="enableElement(this.form.elements['jform_videofile'], this.form.elements['jform_videofile_text']);">
				<?php echo $this->form->getInput('videofile');
				if ($this->params->get('path_mode_video', 0) < 2) {
				$path = !$this->params->get('path_mode_video', 0) ? '/'.$this->params->get('path').'/' : ''; ?>
					<img class="pointer" onClick="window.location.href='<?php echo JRoute::_('index.php?view=frontendupload&amp;type=video') ;?>&amp;file0='+document.fu_createsermon.jform_audiofile_text.value+'&amp;file1=<?php echo $path; ?>'+document.fu_createsermon.jform_videofile.value;" src="media/com_sermonspeaker/icons/16/glasses.png" alt="lookup ID3" title="lookup ID3">
				<?php } ?>
				<div id="infoUpload2" class="intend">
					<span id="btnUpload2"></span>
					<button id="btnCancel2" type="button" onclick="cancelQueue(upload2);" class="ss-hide" disabled="disabled">Cancel</button>
				</div>
			<div class="clr"></div>

			<?php echo $this->form->getLabel('sermon_scripture'); ?>
			<input class="inputbox" type="text" name="sermon_scripture" id="sermon_scripture" size="50" maxlength="250" value="<?php echo $this->form->getValue('sermon_scripture'); ?>" />
				<?php $tag = $this->params->get('plugin_tag'); ?>
				<img class="pointer" onClick="sendText(document.fu_createsermon.sermon_scripture,'<?php echo $tag[0]; ?>','<?php echo $tag[1]; ?>')" src='<?php echo JURI::root(); ?>/media/com_sermonspeaker/images/blue_tag.png'><br />
			<?php echo $this->form->getLabel('sermon_date'); ?>
			<?php echo $this->form->getInput('sermon_date'); ?>
			<br />
			<?php echo $this->form->getLabel('sermon_number'); ?>
			<?php echo $this->form->getInput('sermon_number'); ?>
			<br />
			<?php echo $this->form->getLabel('sermon_time'); ?>
			<?php echo $this->form->getInput('sermon_time'); ?>
			<br />
			<?php echo $this->form->getLabel('speaker_id'); ?>
			<?php echo $this->form->getInput('speaker_id'); ?>
			<br />
			<?php echo $this->form->getLabel('series_id'); ?>
			<?php echo $this->form->getInput('series_id'); ?>
			<br />
			<?php echo $this->form->getLabel('notes'); ?>
			<?php echo $this->form->getInput('notes'); ?>
			<br /><br />
			<?php echo $this->form->getLabel('catid'); ?>
			<?php echo $this->form->getInput('catid'); ?>
			<br />
			<?php echo $this->form->getLabel('state'); ?>
			<?php echo $this->form->getInput('state'); ?>
			<br />
			<?php echo $this->form->getLabel('podcast'); ?>
			<?php echo $this->form->getInput('podcast'); ?>
			<br />

			<?php echo $this->form->getLabel('addfile'); ?>
			<input type="radio" name="sel3" value="0" onclick="enableElement(this.form.elements['jform_addfile_text'], this.form.elements['jform_addfile']);" checked>
				<input class="inputbox" type="text" name="jform[addfile]" id="jform_addfile_text" size="47" maxlength="250" value="" /><br />
			<div class="label">&nbsp;</div>
			<input type="radio" name="sel3" value="1" onclick="enableElement(this.form.elements['jform_addfile'], this.form.elements['jform_addfile_text']);">
				<?php echo $this->form->getInput('addfile'); ?>
				<div id="infoUpload3" class="intend">
					<span id="btnUpload3"></span>
					<button id="btnCancel3" type="button" onclick="cancelQueue(upload3);" class="ss-hide" disabled="disabled">Cancel</button>
				</div>
			<br />
			<?php echo $this->form->getLabel('addfileDesc'); ?>
			<?php echo $this->form->getInput('addfileDesc'); ?>
			<br />
			<div>
				<?php echo JHtml::_('form.token'); ?>
				<input type="submit" class="submit" value="<?php echo JText::_('JSAVE'); ?>">
				<input type="reset" value=" <?php echo JText::_('COM_SERMONSPEAKER_FU_RESET'); ?> ">
			</div>
		</form>
	</div>
	<?php echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?>
</div>