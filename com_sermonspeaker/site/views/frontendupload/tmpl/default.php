<?php
defined('_JEXEC') or die('Restricted access');
JHtml::stylesheet('com_sermonspeaker/frontendupload.css', '', true);
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

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

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=frontendupload&s_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form form-vertical">
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('frontendupload.save')">
					<i class="icon-ok"></i> <?php echo JText::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('frontendupload.cancel')">
					<i class="icon-cancel"></i> <?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
		</div>
		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('JEDITOR') ?></a></li>
				<li><a href="#files" data-toggle="tab"><?php echo JText::_('COM_SERMONSPEAKER_FU_FILES') ?></a></li>
				<li><a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS') ?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_SERMONSPEAKER_PUBLISHING') ?></a></li>
				<li><a href="#language" data-toggle="tab"><?php echo JText::_('JFIELD_LANGUAGE_LABEL') ?></a></li>
				<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('COM_SERMONSPEAKER_METADATA') ?></a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="editor">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('title'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('title'); ?>
						</div>
					</div>

					<?php if (is_null($this->item->id)): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('alias'); ?>
							</div>
						</div>
					<?php endif; ?>

					<?php echo $this->form->getInput('notes'); ?>
				</div>
				<div class="tab-pane" id="files">
					<div id="upload_limit" class="well well-small ss-hide">
						<?php echo JText::sprintf('COM_SERMONSPEAKER_UPLOAD_LIMIT', $this->upload_limit); ?>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('audiofile'); ?>
						</div>
						<div class="controls">
							<div class="input-prepend input-append">
								<div id="audiofile_text_icon" class="btn add-on icon-checkmark" onclick="toggleElement('audiofile', 0);"> </div>
								<input name="jform[audiofile]" id="jform_audiofile_text" value="<?php echo $this->form->getValue('audiofile'); ?>" class="inputbox" size="100" type="text">
								<div class="btn add-on hasTip icon-wand" onclick="lookup(document.getElementById('jform_audiofile_text'));" title="<?php echo JText::_('COM_SERMONSPEAKER_LOOKUP'); ?>"> </div>
							</div>
							<div class="input-prepend input-append">
								<div id="audiofile_icon" class="btn add-on icon-cancel" onclick="toggleElement('audiofile', 1);"> </div>
								<?php echo $this->form->getInput('audiofile');
								if (!$this->params->get('path_mode_audio', 0)) : ?>
									<div class="btn add-on hasTip icon-wand" onclick="lookup(document.getElementById('jform_audiofile'))" title="<?php echo JText::_('COM_SERMONSPEAKER_LOOKUP'); ?>"> </div>
								<?php endif; ?>
							</div>
							<?php if($this->params->get('enable_flash')) : ?>
								<div id="infoUpload1">
									<span id="btnUpload1"></span>
									<button id="btnCancel1" type="button" onclick="cancelQueue(upload1);" class="ss-hide upload_button" disabled="disabled">Cancel</button>
									<span id="audiopathinfo" class="label label-info ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
										<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO');
										if ($this->s3audio) :
											echo ' http://'.$this->prefix.'.amazonaws.com/'.$this->bucket.'/';
										else :
											echo ' /'.trim($this->params->get('path'), '/').'/<span id="audiopathdate" class="pathdate">'.$this->append_date.'</span><span id="audiopathlang" class="pathlang">'.$this->append_lang.'</span>';
										endif; ?>
									</span>
								</div>
							<?php endif; ?>
						</div>
						<br />
						<div class="control-label">
							<?php echo $this->form->getLabel('videofile'); ?>
						</div>
						<div class="controls">
							<div class="input-prepend input-append">
								<div id="videofile_text_icon" class="btn add-on icon-checkmark" onclick="toggleElement('videofile', 0);"> </div>
								<input name="jform[videofile]" id="jform_videofile_text" value="<?php echo $this->form->getValue('videofile'); ?>" class="inputbox" size="100" type="text">
								<div class="btn add-on hasTip icon-wand" onclick="lookup(document.getElementById('jform_videofile_text'));" title="<?php echo JText::_('COM_SERMONSPEAKER_LOOKUP'); ?>"> </div>
								<?php if ($this->params->get('googlepicker', 0)) : ?>
									<div class="btn add-on hasTip" onclick="createVideoPicker();" title="<?php echo JText::_('COM_SERMONSPEAKER_GOOGLEPICKER_TIP'); ?>"><img src="<?php echo JURI::root(); ?>media/com_sermonspeaker/icons/16/drive.png"></div>
								<?php endif; ?>
							</div>
							<div class="input-prepend input-append">
								<div id="videofile_icon" class="btn add-on icon-cancel" onclick="toggleElement('videofile', 1);"> </div>
								<?php echo $this->form->getInput('videofile'); 
								if ($this->params->get('path_mode_video', 0) < 2) { ?>
									<div class="btn add-on hasTip icon-wand pointer" onclick="lookup(document.getElementById('jform_videofile'));" title="<?php echo JText::_('COM_SERMONSPEAKER_LOOKUP'); ?>"> </div>
								<?php } ?>
							</div>
							<?php if($this->params->get('enable_flash')) : ?>
								<div id="infoUpload2">
									<span id="btnUpload2"></span>
									<button id="btnCancel2" type="button" onclick="cancelQueue(upload2);" class="ss-hide upload_button" disabled="disabled">Cancel</button>
									<span id="videopathinfo" class="label label-info ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
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
						<br />
						<div class="control-label">
							<?php echo $this->form->getLabel('addfile'); ?>
						</div>
						<div class="controls">
							<div class="input-prepend input-append">
								<div id="addfile_text_icon" class="btn add-on icon-checkmark" onclick="toggleElement('addfile', 0);"> </div>
								<input name="jform[addfile]" id="jform_addfile_text" value="<?php echo $this->form->getValue('addfile'); ?>" class="inputbox" size="100" type="text">
								<?php if ($this->params->get('googlepicker', 0)) : ?>
									<div class="btn add-on hasTip" onclick="createAddfilePicker();" title="<?php echo JText::_('COM_SERMONSPEAKER_GOOGLEPICKER_TIP'); ?>"><img src="<?php echo JURI::root(); ?>media/com_sermonspeaker/icons/16/drive.png"></div>
								<?php endif; ?>
							</div>
							<div class="input-prepend input-append">
								<div id="addfile_icon" class="btn add-on icon-cancel" onclick="toggleElement('addfile', 1);"> </div>
								<?php echo $this->form->getInput('addfile'); ?>
							</div>
							<?php if($this->params->get('enable_flash')) : ?>
								<div id="infoUpload3">
									<span id="btnUpload3"></span>
									<button id="btnCancel3" type="button" onclick="cancelQueue(upload3);" class="ss-hide upload_button" disabled="disabled">Cancel</button>
									<span id="addfilepathinfo" class="label label-info ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
										<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO').' /'.trim($this->params->get('path_addfile'), '/').'/<span id="addfilepathdate" class="pathdate">'.$this->append_date.'</span><span id="addfilepathlang" class="pathlang">'.$this->append_lang.'</span>'; ?>
									</span>
								</div>
							<?php endif; ?>
						</div>
						<div class="control-label">
							<?php echo $this->form->getLabel('addfileDesc'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('addfileDesc'); ?>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="details">
					<?php foreach($this->form->getFieldset('detail') as $field): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="tab-pane" id="publishing">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('catid'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('catid'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('tags', 'metadata'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('tags', 'metadata'); ?>
						</div>
					</div>
					<?php if ($this->user->authorise('core.edit.state', 'com_sermonspeaker')): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('state'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('state'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('podcast'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('podcast'); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="tab-pane" id="language">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('language'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('language'); ?>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="metadata">
					<?php foreach($this->form->getFieldset('metadata') as $field): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
	<?php if ($this->params->get('enable_non_flash')) : ?>
		<hr />
		<form action="<?php echo JURI::root(); ?>index.php?option=com_sermonspeaker&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JSession::getFormToken();?>=1" id="uploadForm" name="uploadForm" class="form-validate form form-vertical" method="post" enctype="multipart/form-data">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_FU_SELECTFILE'); ?></legend>
			<div class="control-group">
				<div class="control-label">
					<label for="upload-audiofile"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_AUDIOFILE_LABEL'); ?></label>
				</div>
				<div class="controls">
					<input type="file" size="50" id="upload-audiofile" name="Filedata[]" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label for="upload-videofile"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_VIDEOFILE_LABEL'); ?></label>
				</div>
				<div class="controls">
					<input type="file" size="50" id="upload-videofile" name="Filedata[]" />
				</div>
			</div>
			<div class="well well-small">
				<div><?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO'); ?>
				<span class="label label-info">/<?php echo trim($this->params->get('path'), '/').'/';
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
				<?php endif; ?></span>.</div>
				<div><?php echo JText::sprintf('COM_SERMONSPEAKER_UPLOAD_LIMIT', $this->upload_limit); ?></div>
			</div>
			<button type="submit" class="btn">
				<i class="icon-upload"></i> <?php echo JText::_('COM_SERMONSPEAKER_FU_START_UPLOAD'); ?>
			</button>
			<input type="hidden" name="return-url" value="<?php echo base64_encode($self); ?>" />
		</form>
	<?php endif; ?>
</div>