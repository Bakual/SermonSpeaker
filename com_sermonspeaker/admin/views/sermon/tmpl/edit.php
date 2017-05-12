<?php
// no direct access

defined('_JEXEC') or die;

use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
JHtml::_('behavior.tabstate');
JHtml::_('bootstrap.tooltip');

$this->ignore_fieldsets = array('general', 'files', 'info', 'custom', 'detail', 'publishingdata', 'jmetadata', 'item_associations');

$uri = JUri::getInstance();
$uri->delVar('file');
$uri->delVar('type');
$self = $uri->toString();

// Check if tmpl=component was set (needed for com_associations)
$jinput = JFactory::getApplication()->input;
$tmpl   = $jinput->getCmd('tmpl') === 'component' ? '&tmpl=component' : '';
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&layout=edit&id='.(int) $this->item->id . $tmpl); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL', true)); ?>
		<div class="row">
			<div class="col-md-9">
				<fieldset class="adminform">
					<?php echo $this->form->getInput('notes'); ?>
				</fieldset>
			</div>
			<div class="col-md-3">
				<div class="card card-block card-light">
					<?php $this->fields = array('state', 'podcast', 'catid', 'language', 'tags', 'version_note'); ?>
					<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
					<?php unset($this->fields); ?>
				</div>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'files', JText::_('COM_SERMONSPEAKER_TAB_FILES_DETAILS', true)); ?>
		<div class="row">
			<div class="col-md-6">
				<div id="upload_limit" class="alert alert-info">
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
			<div class="col-md-6">
				<?php foreach($this->form->getFieldset('detail') as $field): ?>
					<?php echo $field->renderField(); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
			<div class="row">
				<div class="col-md-6">
					<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
				<div class="col-md-6">
					<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if (Associations::isEnabled()) :
			echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true));
				echo JLayoutHelper::render('joomla.edit.associations', $this);
			echo JHtml::_('bootstrap.endTab');
		endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $jinput->getCmd('return');?>" />
		<input type="hidden" name="forcedLanguage" value="<?php echo $jinput->get('forcedLanguage', '', 'cmd'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
