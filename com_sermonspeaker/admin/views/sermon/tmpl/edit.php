<?php
// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$this->ignore_fieldsets = array('general', 'info', 'custom', 'detail', 'jmetadata', 'item_associations');

$uri = JUri::getInstance();
$uri->delVar('file');
$uri->delVar('type');
$self = $uri->toString();

$app   = JFactory::getApplication();
$input = $app->input;
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

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL', true)); ?>
			<div class="row-fluid">
				<div class="span9">
					<fieldset class="adminform">
						<?php echo $this->form->getInput('notes'); ?>
					</fieldset>
				</div>
				<div class="span3">
					<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'myTab', 'files', JText::_('COM_SERMONSPEAKER_TAB_FILES_DETAILS', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<div id="upload_limit" class="well well-small">
						<?php echo JText::sprintf('COM_SERMONSPEAKER_UPLOAD_LIMIT', $this->upload_limit); ?>
					</div>
					<div id="audiofile_drop" class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('audiofile'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('audiofile'); ?>
							<div id="audiopathinfo" class="pathinfo hasTooltip" title="<?php echo JHtml::tooltipText(JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'), '', 0); ?>">
								<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO');
								if ($this->s3audio) :
									echo ' https://' . $this->domain . '/';
								else :
									echo ' /' . trim($this->params->get('path_audio'), '/') . '/';
								endif;
								echo '<span id="audiopathdate" class="pathdate">' . $this->append_date . '</span><span id="audiopathlang" class="pathlang">' . $this->append_lang . '</span>'; ?>
							</div>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('audiofilesize'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('audiofilesize'); ?>
						</div>
					</div>
					<hr />
					<div id="videofile_drop" class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('videofile'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('videofile'); ?>
							<div id="videopathinfo" class="pathinfo hasTooltip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
								<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO');
								if ($this->s3video) :
									echo ' https://' . $this->domain . '/';
								else :
									echo ' /' . trim($this->params->get('path_video'), '/') . '/';
								endif;
								echo '<span id="videopathdate" class="pathdate">' . $this->append_date . '</span><span id="videopathlang" class="pathlang">' . $this->append_lang . '</span>'; ?>
							</div>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('videofilesize'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('videofilesize'); ?>
						</div>
					</div>
					<hr />
					<div id="addfile_drop" class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('addfile'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('addfile'); ?>
							<div id="addfilepathinfo" class="pathinfo hasTooltip" title="<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
								<?php echo JText::_('COM_SERMONSPEAKER_UPLOADINFO') . ' /' . trim($this->params->get('path_addfile'), '/')
									. '/<span id="addfilepathdate" class="pathdate">' . $this->append_date . '</span><span id="addfilepathlang" class="pathlang">'
									. $this->append_lang . '</span>'; ?>
							</div>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('addfileDesc'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('addfileDesc'); ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<?php foreach($this->form->getFieldset('detail') as $field): ?>
						<?php echo $field->getControlGroup(); ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab');
		if ($this->params->get('custom1') OR $this->params->get('custom2')) :
			echo JHtml::_('bootstrap.addTab', 'myTab', 'custom', JText::_('COM_SERMONSPEAKER_FIELDSET_CUSTOM_LABEL', true)); ?>
				<div class="row-fluid">
					<div class="span6">
						<?php foreach($this->form->getFieldset('custom') as $field): ?>
							<?php echo $field->getControlGroup(); ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php echo JHtml::_('bootstrap.endTab');
		endif;
		if (JLanguageAssociations::isEnabled()) :
			echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true));
				echo $this->loadTemplate('associations');
			echo JHtml::_('bootstrap.endTab');
		endif;
		echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
