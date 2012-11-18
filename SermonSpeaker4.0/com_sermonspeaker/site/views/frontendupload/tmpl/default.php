<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::stylesheet('com_sermonspeaker/frontendupload.css', '', true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$uri = JURI::getInstance();
$uri->delVar('file');
$uri->delVar('file0');
$uri->delVar('file1');
$uri->delVar('type');
$self = $uri->toString();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'frontendupload.cancel' || <?php if ($this->params->get('enable_flash')) echo "navigator.appName == 'Microsoft Internet Explorer' || "; ?>document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('notes')->save(); ?>
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
<?php endif;
if ($this->params->get('enable_non_flash')) : ?>
	<form action="<?php echo JURI::root(); ?>index.php?option=com_sermonspeaker&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JSession::getFormToken();?>=1" id="uploadForm" name="uploadForm" class="form-validate" method="post" enctype="multipart/form-data">
		<fieldset id="upload-noflash" class="actions">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_FU_SELECTFILE'); ?></legend>
			<label for="upload-file" class="label"><?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO'); ?></label>
			/<?php echo trim($this->params->get('path'), '/').'/';
			if ($this->params->get('append_path', 0)) :
				$time	= ($this->item->sermon_date AND $this->item->sermon_date != '0000-00-00 00:00:00') ? strtotime($this->item->sermon_date) : time();
				?><input type="text" id="year" size="4" name="year" value="<?php echo date('Y', $time); ?>" />/<input type="text" id="month" size="2" name="month" value="<?php echo date('m', $time); ?>" />/<?php 
			endif;
			if ($this->params->get('append_path_lang', 0)) :
				$lang = $this->item->language;
				if (!$lang || $lang == '*') :
					$lang	= JFactory::getLanguage()->getTag();
				endif;
				?><input type="text" id="lang" size="5" name="lang" value="<?php echo $lang; ?>" />/
			<?php endif; ?>
			<br />
			<label for="upload-file" class="label"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_AUDIOFILE_LABEL'); ?></label>
			<input type="file" size="50" id="upload-file" name="Filedata[]" /><br />
			<label for="upload-file" class="label"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_VIDEOFILE_LABEL'); ?></label>
			<input type="file" size="50" id="upload-file" name="Filedata[]" /><br />
			<input type="submit" class="submit" value="<?php echo JText::_('COM_SERMONSPEAKER_FU_START_UPLOAD'); ?>" />
			<input type="hidden" name="return-url" value="<?php echo base64_encode($self); ?>" />
		</fieldset>
		<?php if($this->params->get('enable_flash')): ?>
			<div id="loading" class="message">Flash is loading... please wait...</div>
		<?php endif; ?>
	</form>
<?php endif; ?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=frontendupload&s_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<fieldset>
		<legend><?php echo JText::_('JEDITOR'); ?></legend>
		<div class="formelm">
			<?php echo $this->form->getLabel('sermon_title'); ?>
			<?php echo $this->form->getInput('sermon_title'); ?>
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
			<div class="formelm">
				<?php echo $this->form->getLabel('podcast'); ?>
				<?php echo $this->form->getInput('podcast'); ?>
			</div>
		<?php endif; ?>
		<div class="formelm-buttons">
			<button type="button" onclick="Joomla.submitbutton('frontendupload.apply')">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="Joomla.submitbutton('frontendupload.save')">
				<?php echo JText::_('COM_SERMONSPEAKER_SAVEANDCLOSE') ?>
			</button>
			<button type="button" onclick="Joomla.submitbutton('frontendupload.cancel')">
				<?php echo JText::_('JCANCEL') ?>
			</button>
		</div>
		<div>
			<?php echo $this->form->getLabel('notes'); ?>
			<?php echo $this->form->getInput('notes'); ?>
		</div>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_SERMONSPEAKER_FU_FILES'); ?></legend>
		<div class="formelm">
			<?php echo $this->form->getLabel('audiofile'); ?>
			<input type="radio" name="sel1" value="0" onclick="enableElement(this.form.elements['jform_audiofile_text'], this.form.elements['jform_audiofile']);" checked>
				<input class="inputbox" type="text" name="jform[audiofile]" id="jform_audiofile_text" size="47" maxlength="250" value="<?php echo $this->form->getValue('audiofile'); ?>" />
					<img class="pointer" onclick="lookup(document.adminForm.jform_audiofile_text);" src="media/com_sermonspeaker/icons/16/glasses.png" alt="lookup ID3" title="lookup ID3"><br />
			<label>&nbsp;</label>
			<input type="radio" name="sel1" value="1" onclick="enableElement(this.form.elements['jform_audiofile'], this.form.elements['jform_audiofile_text']);">
				<?php echo $this->form->getInput('audiofile');
				if (!$this->params->get('path_mode_audio', 0)) { ?>
					<img class="pointer" onclick="lookup(document.adminForm.jform_audiofile);" src="media/com_sermonspeaker/icons/16/glasses.png" alt="lookup ID3" title="lookup ID3"><br />
				<?php } ?>
				<?php if($this->params->get('enable_flash')) : ?>
					<div id="infoUpload1" class="intend">
						<span id="btnUpload1"></span>
						<button id="btnCancel1" type="button" onclick="cancelQueue(upload1);" class="ss-hide" disabled="disabled">Cancel</button>
						<span id="audiopathinfo" class="pathinfo ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
							<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO');
							if ($this->s3audio):
								echo ' http://'.$this->prefix.'.amazonaws.com/'.$this->bucket.'/';
							else:
								echo ' /'.trim($this->params->get('path'), '/').'/<span id="audiopathdate" class="pathdate">'.$this->append_date.'</span><span id="audiopathlang" class="pathlang">'.$this->append_lang.'</span>';
							endif; ?>
						</span>
					</div>
				<?php endif; ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('videofile'); ?>
			<input type="radio" name="sel2" value="0" onclick="enableElement(this.form.elements['jform_videofile_text'], this.form.elements['jform_videofile']);" checked>
				<input class="inputbox" type="text" name="jform[videofile]" id="jform_videofile_text" size="47" maxlength="250" value="<?php echo $this->form->getValue('videofile'); ?>" />
					<img class="pointer" onclick="lookup(document.adminForm.jform_videofile_text);" src="media/com_sermonspeaker/icons/16/glasses.png" alt="lookup ID3" title="lookup ID3"><br />
			<label>&nbsp;</label>
			<input type="radio" name="sel2" value="1" onclick="enableElement(this.form.elements['jform_videofile'], this.form.elements['jform_videofile_text']);">
				<?php echo $this->form->getInput('videofile');
				if ($this->params->get('path_mode_video', 0) < 2) { ?>
					<img class="pointer" onclick="lookup(document.adminForm.jform_videofile);" src="media/com_sermonspeaker/icons/16/glasses.png" alt="lookup ID3" title="lookup ID3"><br />
				<?php } ?>
				<?php if($this->params->get('enable_flash')) : ?>
					<div id="infoUpload2" class="intend">
						<span id="btnUpload2"></span>
						<button id="btnCancel2" type="button" onclick="cancelQueue(upload2);" class="ss-hide" disabled="disabled">Cancel</button>
						<span id="videopathinfo" class="pathinfo ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
							<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO');
							if ($this->s3video):
								echo ' http://'.$this->prefix.'.amazonaws.com/'.$this->bucket.'/';
							else:
								echo ' /'.trim($this->params->get('path'), '/').'/<span id="videopathdate" class="pathdate">'.$this->append_date.'</span><span id="videopathlang" class="pathlang">'.$this->append_lang.'</span>';
							endif; ?>
						</span>
					</div>
				<?php endif; ?>
		</div>
		<div class="formelm">
			<?php echo $this->form->getLabel('addfile'); ?>
			<input type="radio" name="sel3" value="0" onclick="enableElement(this.form.elements['jform_addfile_text'], this.form.elements['jform_addfile']);" checked>
				<input class="inputbox" type="text" name="jform[addfile]" id="jform_addfile_text" size="47" maxlength="250" value="<?php echo $this->form->getValue('addfile'); ?>" /><br />
			<label>&nbsp;</label>
			<input type="radio" name="sel3" value="1" onclick="enableElement(this.form.elements['jform_addfile'], this.form.elements['jform_addfile_text']);">
				<?php echo $this->form->getInput('addfile'); ?>
				<?php if($this->params->get('enable_flash')) : ?>
					<div id="infoUpload3" class="intend">
						<span id="btnUpload3"></span>
						<button id="btnCancel3" type="button" onclick="cancelQueue(upload3);" class="ss-hide" disabled="disabled">Cancel</button>
						<span id="addfilepathinfo" class="pathinfo ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
							<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO').' /'.trim($this->params->get('path_addfile'), '/').'/<span id="addfilepathdate" class="pathdate">'.$this->append_date.'</span><span id="addfilepathlang" class="pathlang">'.$this->append_lang.'</span>'; ?>
						</span>
					</div>
				<?php endif; ?>
			<?php echo $this->form->getLabel('addfileDesc'); ?>
			<?php echo $this->form->getInput('addfileDesc'); ?>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('JDETAILS'); ?></legend>
		<?php foreach($this->form->getFieldset('detail') as $field): ?>
			<div class="formelm">
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
				<?php if ($field->fieldname == 'picture'): ?>
					<div style="clear:both"></div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<div class="formelm-buttons">
			<button type="button" onclick="Joomla.submitbutton('frontendupload.apply')">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="Joomla.submitbutton('frontendupload.save')">
				<?php echo JText::_('COM_SERMONSPEAKER_SAVEANDCLOSE') ?>
			</button>
			<button type="button" onclick="Joomla.submitbutton('frontendupload.cancel')">
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
<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="login-form">
	<div class="logout-button">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo base64_encode(JURI::Root()); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
</div>