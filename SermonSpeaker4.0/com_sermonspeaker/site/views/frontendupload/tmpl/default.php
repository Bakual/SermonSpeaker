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

<div id="ss-frup-container">
	<h1><?php echo JText::_('COM_SERMONSPEAKER_FU_NEWSERMON'); ?></h1>
	<div id="ss-frup-form">
		<form action="<?php echo JURI::base(); ?>index.php?option=com_sermonspeaker&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JUtility::getToken();?>=1" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data">
			<fieldset id="upload-noflash" class="actions">
				<legend><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP1'); ?></legend>
				<label for="upload-file" class="label"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_AUDIOFILE_LABEL'); ?></label>
				<input type="file" size="50" id="upload-file" name="Filedata[]" /><br />
				<label for="upload-file" class="label"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_VIDEOFILE_LABEL'); ?></label>
				<input type="file" size="50" id="upload-file" name="Filedata[]" /><br />
				<input type="submit" class="submit" value="<?php echo JText::_('COM_SERMONSPEAKER_FU_START_UPLOAD'); ?>" />
				<input type="hidden" name="return-url" value="<?php echo base64_encode($self); ?>" />
			</fieldset>
		</form>
		<form action="<?php echo JRoute::_('index.php?view=frontendupload&task=frontendupload.save'); ?>" name="fu_createsermon" id="fu_createsermon" method="post" enctype="multipart/form-data" class="form-validate">
			<?php echo $this->form->getLabel('sermon_title'); ?>
			<?php echo $this->form->getInput('sermon_title'); ?>
			<br />
			<?php echo $this->form->getLabel('alias'); ?>
			<?php echo $this->form->getInput('alias'); ?>
			<br />

			<label for="audiofile"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_AUDIOFILE_LABEL'); ?></label>
			<input type="radio" name="sel1" value="0" onclick="enableElement(this.form.elements['jform_audiofile_text'], this.form.elements['jform_audiofile']);" checked>
				<input class="text_area" type="text" name="jform[audiofile]" id="jform_audiofile_text" size="47" maxlength="250" value="<?php echo $this->form->getValue('audiofile'); ?>" />
					<img class="pointer" onClick="window.location.href='<?php echo JRoute::_('index.php?view=frontendupload&amp;type=audio') ;?>&amp;file0='+document.fu_createsermon.jform_audiofile_text.value+'&amp;file1='+document.fu_createsermon.jform_videofile_text.value;" src="media/com_sermonspeaker/images/find.png" alt="lookup ID3" title="lookup ID3"><br />
			<div class="label">&nbsp;</div>
			<input type="radio" name="sel1" value="1" onclick="enableElement(this.form.elements['jform_audiofile'], this.form.elements['jform_audiofile_text']);">
				<?php echo $this->form->getInput('audiofile'); ?>
					<img class="pointer" onClick="window.location.href='<?php echo JRoute::_('index.php?view=frontendupload&amp;type=audio') ;?>&amp;file0=<?php echo '/'.$this->params->get('path').'/'; ?>'+document.fu_createsermon.jform_audiofile.value+'&amp;file1='+document.fu_createsermon.jform_videofile_text.value;" src="media/com_sermonspeaker/images/find.png" alt="lookup ID3" title="lookup ID3">
				<div id="infoUpload1" class="intend">
					<span id="btnUpload1"></span>
					<button id="btnCancel1" type="button" onclick="cancelQueue(upload1);" class="hide" disabled="disabled">Cancel</button>
				</div>
			<div class="clr"></div>
			
			<label for="videofile"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_VIDEOFILE_LABEL'); ?></label>
			<input type="radio" name="sel2" value="0" onclick="enableElement(this.form.elements['jform_videofile_text'], this.form.elements['jform_videofile']);" checked>
				<input class="text_area" type="text" name="jform[videofile]" id="jform_videofile_text" size="47" maxlength="250" value="<?php echo $this->form->getValue('videofile'); ?>" />
					<img class="pointer" onClick="window.location.href='<?php echo JRoute::_('index.php?view=frontendupload&amp;type=video') ;?>&amp;file0='+document.fu_createsermon.jform_audiofile_text.value+'&amp;file1='+document.fu_createsermon.jform_videofile_text.value;" src="media/com_sermonspeaker/images/find.png" alt="lookup ID3" title="lookup ID3"><br />
			<div class="label">&nbsp;</div>
			<input type="radio" name="sel2" value="1" onclick="enableElement(this.form.elements['jform_videofile'], this.form.elements['jform_videofile_text']);">
				<?php echo $this->form->getInput('videofile'); ?>
				<img class="pointer" onClick="window.location.href='<?php echo JRoute::_('index.php?view=frontendupload&amp;type=video') ;?>&amp;file0='+document.fu_createsermon.jform_audiofile_text.value+'&amp;file1=<?php echo '/'.$this->params->get('path').'/'; ?>'+document.fu_createsermon.jform_videofile.value;" src="media/com_sermonspeaker/images/find.png" alt="lookup ID3" title="lookup ID3">
				<div id="infoUpload2" class="intend">
					<span id="btnUpload2"></span>
					<button id="btnCancel2" type="button" onclick="cancelQueue(upload1);" class="hide" disabled="disabled">Cancel</button>
				</div>
			<div class="clr"></div>

			<label for="sermon_scripture"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?></label>
			<input class="text_area" type="text" name="sermon_scripture" id="sermon_scripture" size="50" maxlength="250" value="<?php echo $this->form->getValue('sermon_scripture'); ?>" />
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

			<label for="addfile_txt"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></label>
			<input type="radio" name="sel3" value="0" onclick="enableElement(this.form.elements['jform_addfile_text'], this.form.elements['jform_addfile']);" checked>
				<input class="text_area" type="text" name="jform[addfile]" id="jform_addfile_text" size="47" maxlength="250" value="" /><br />
			<div class="label">&nbsp;</div>
			<input type="radio" name="sel3" value="1" onclick="enableElement(this.form.elements['jform_addfile'], this.form.elements['jform_addfile_text']);">
				<?php echo $this->form->getInput('addfile'); ?>
			<br />
			<label for="addfileDesc"><?php echo JText::_('COM_SERMONSPEAKER_FU_ADDFILEDESC'); ?></label>
			<input class="text_area" type="text" name="addfileDesc" id="addfileDesc" size="50" maxlength="250" value="" /><br />
			<div>
				<?php echo JHtml::_('form.token'); ?>
				<input type="submit" class="submit" value="<?php echo JText::_('JSAVE'); ?>">
				<input type="reset" value=" <?php echo JText::_('COM_SERMONSPEAKER_FU_RESET'); ?> ">
			</div>
		</form>
	</div>
	<?php echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?>
</div>