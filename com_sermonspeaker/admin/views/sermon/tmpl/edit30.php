<?php
// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$uri = JURI::getInstance();
$uri->delVar('file');
$uri->delVar('type');
$self = $uri->toString();

$input = JFactory::getApplication()->input;
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'sermon.cancel' || navigator.appName == 'Microsoft Internet Explorer' || navigator.userAgent.match(/Trident/) || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('notes')->save(); ?>
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('JDETAILS');?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></a></li>
				<?php if ($this->params->get('custom1') OR $this->params->get('custom2')): ?>
					<li><a href="#custom" data-toggle="tab"><?php echo JText::_('COM_SERMONSPEAKER_FIELDSET_CUSTOM_LABEL');?></a></li>
				<?php endif; ?>
				<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS');?></a></li>
			</ul>

			<div class="tab-content">
				<!-- Begin Tabs -->
				<div class="tab-pane active" id="general">
					<fieldset class="adminform">
						<div class="control-group form-inline">
							<?php echo $this->form->getLabel('sermon_title'); ?> <?php echo $this->form->getInput('sermon_title'); ?> <?php echo $this->form->getLabel('catid'); ?> <?php echo $this->form->getInput('catid'); ?>
						</div>
						<?php echo $this->form->getInput('notes'); ?>
					</fieldset>
					<div class="row-fluid">
						<div class="span6">
							<h4>Files</h4>
							<div id="upload_limit_audio" class="well well-small ss-hide">
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
										<div class="btn add-on hasTip icon-wand" onclick="lookup(document.getElementById('jform_audiofile_text'))" title="<?php echo JText::_('COM_SERMONSPEAKER_LOOKUP'); ?>"> </div>
									</div>
									<div class="input-prepend input-append">
										<div id="audiofile_icon" class="btn add-on icon-cancel" onclick="toggleElement('audiofile', 1);"> </div>
										<?php echo $this->form->getInput('audiofile');
										if (!$this->params->get('path_mode_audio', 0)) : ?>
											<div class="btn add-on hasTip icon-wand" onclick="lookup(document.getElementById('jform_audiofile'))" title="<?php echo JText::_('COM_SERMONSPEAKER_LOOKUP'); ?>"> </div>
										<?php endif; ?>
									</div>
									<div id="infoUpload1" class="intend">
										<span id="btnUpload1"></span>
										<button id="btnCancel1" type="button" onclick="cancelQueue(upload1);" class="ss-hide upload_button" disabled="disabled">Cancel</button>
										<span id="audiopathinfo" class="pathinfo ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
											<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO');
											if ($this->s3audio) :
												echo ' http://'.$this->prefix.'.amazonaws.com/'.$this->bucket.'/';
											else :
												echo ' /'.trim($this->params->get('path'), '/').'/<span id="audiopathdate" class="pathdate">'.$this->append_date.'</span><span id="audiopathlang" class="pathlang">'.$this->append_lang.'</span>';
											endif; ?>
										</span>
									</div>
								</div>
								<div class="control-label">
									<?php echo $this->form->getLabel('audiofilesize'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('audiofilesize'); ?>
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
									<div id="infoUpload2" class="intend">
										<span id="btnUpload2"></span>
										<button id="btnCancel2" type="button" onclick="cancelQueue(upload2);" class="ss-hide upload_button" disabled="disabled">Cancel</button>
										<span id="videopathinfo" class="pathinfo ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
											<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO');
											if ($this->s3video):
												echo ' http://'.$this->prefix.'.amazonaws.com/'.$this->bucket.'/';
											else:
												echo ' /'.trim($this->params->get('path'), '/').'/<span id="videopathdate" class="pathdate">'.$this->append_date.'</span><span id="videopathlang" class="pathlang">'.$this->append_lang.'</span>';
											endif; ?>
										</span>
									</div>
								</div>
								<div class="control-label">
									<?php echo $this->form->getLabel('videofilesize'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('videofilesize'); ?>
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
									<div id="infoUpload3" class="intend">
										<span id="btnUpload3"></span>
										<button id="btnCancel3" type="button" onclick="cancelQueue(upload3);" class="ss-hide upload_button" disabled="disabled">Cancel</button>
										<span id="addfilepathinfo" class="pathinfo ss-hide hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
											<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO').' /'.trim($this->params->get('path_addfile'), '/').'/<span id="addfilepathdate" class="pathdate">'.$this->append_date.'</span><span id="addfilepathlang" class="pathlang">'.$this->append_lang.'</span>'; ?>
										</span>
									</div>
									
								</div>
								<div class="control-label">
									<?php echo $this->form->getLabel('addfileDesc'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('addfileDesc'); ?>
								</div>
							</div>
						</div>
						<div class="span6">
							<h4><?php echo JText::_('JDETAILS');?></h4>
							<?php foreach($this->form->getFieldset('detail') as $field): ?>
								<div class="control-group">
									<?php if (!$field->hidden): ?>
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
									<?php endif; ?>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="publishing">
					<div class="row-fluid">
						<div class="span6">
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('alias'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('alias'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('id'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('id'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('created_by'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('created_by'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('created'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('created'); ?>
								</div>
							</div>
						</div>
						<div class="span6">
							<?php if ($this->item->modified_by) : ?>
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('modified_by'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('modified_by'); ?>
									</div>
								</div>
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('modified'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('modified'); ?>
									</div>
								</div>
							<?php endif; ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('hits'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('hits'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php if ($this->params->get('custom1') OR $this->params->get('custom2')): ?>
					<div class="tab-pane" id="custom">
						<div class="row-fluid">
							<div class="span6">
								<?php foreach($this->form->getFieldset('custom') as $field): ?>
									<div class="control-group">
										<?php if (!$field->hidden): ?>
											<div class="control-label">
												<?php echo $field->label; ?>
											</div>
										<?php endif; ?>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<div class="tab-pane" id="metadata">
					<fieldset>
						<?php foreach($this->form->getFieldset('metadata') as $field): ?>
							<div class="control-group">
								<?php if (!$field->hidden): ?>
									<div class="control-label">
										<?php echo $field->label; ?>
									</div>
								<?php endif; ?>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</fieldset>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
		<!-- End Content -->
		<!-- Begin Sidebar -->
		<div class="span2">
			<h4><?php echo JText::_('JDETAILS');?></h4>
			<hr />
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getValue('sermon_title'); ?>
					</div>
				</div>

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

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('language'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('language'); ?>
					</div>
				</div>
			</fieldset>
		</div>
		<!-- End Sidebar -->
	</div>
</form>
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Modal header</h3>
	</div>
	<div class="modal-body">

	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal">Close</button>
		<button class="btn btn-primary">Save changes</button>
	</div>
</div>