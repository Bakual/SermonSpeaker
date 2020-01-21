<?php
// no direct access

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

// Include the component HTML helpers.
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.tooltip');

HTMLHelper::_('script', 'com_contenthistory/admin-history-versions.js', ['version' => 'auto', 'relative' => true]);

$this->ignore_fieldsets = array('general', 'audiofile', 'videofile', 'addfile', 'info', 'detail', 'publishingdata', 'jmetadata', 'item_associations');
$this->useCoreUI = true;

$uri = JUri::getInstance();
$uri->delVar('file');
$uri->delVar('type');
$self = $uri->toString();

// Check if tmpl=component was set (needed for com_associations)
$jinput = Factory::getApplication()->input;
$tmpl   = $jinput->getCmd('tmpl') === 'component' ? '&tmpl=component' : '';
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&layout=edit&id='.(int) $this->item->id . $tmpl); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL', true)); ?>
		<div class="row">
			<div class="col-lg-9">
				<div class="card">
					<div class="card-body">
						<fieldset class="adminform">
							<?php echo $this->form->getLabel('notes'); ?>
							<?php echo $this->form->getInput('notes'); ?>
						</fieldset>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="bg-white px-3">
					<?php $this->fields = array('state', 'catid', 'podcast', 'language', 'tags', 'version_note'); ?>
					<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
					<?php unset($this->fields); ?>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'files', Text::_('COM_SERMONSPEAKER_TAB_FILES_DETAILS')); ?>
		<div class="row">
			<div class="col-12 col-lg-6">
				<div id="upload_limit" class="alert alert-info">
					<?php echo Text::sprintf('COM_SERMONSPEAKER_UPLOAD_LIMIT', $this->upload_limit); ?>
				</div>

				<?php foreach (['audiofile', 'videofile', 'addfile'] as $fieldset) : ?>
					<?php $fields = $this->form->getFieldset($fieldset); ?>
					<fieldset id="<?php echo $fieldset; ?>_drop" class="options-form">
						<legend><?php echo Text::_($this->form->getFieldsets()[$fieldset]->label); ?></legend>
						<div>
							<?php $fileFieldName = array_shift($fields)->fieldname; ?>

							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel($fileFieldName); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput($fileFieldName); ?>
									<div id="audiopathinfo" class="badge badge-info hasTooltip" title="<?php echo HTMLHelper::tooltipText(Text::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'), '', 0); ?>">
										<?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO');
										if ($this->s3audio) :
											echo ' https://' . $this->domain . '/';
										else :
											echo ' /' . trim($this->params->get('path_audio'), '/') . '/';
										endif;
										echo '<span id="audiopathdate" class="pathdate">' . $this->append_date . '</span><span id="audiopathlang" class="pathlang">' . $this->append_lang . '</span>'; ?>
									</div>
								</div>
							</div>

							<?php foreach ($fields as $field) : ?>
								<?php echo $this->form->renderField($field->fieldname); ?>
							<?php endforeach; ?>
						</div>
					</fieldset>
				<?php endforeach; ?>
			</div>
			<div class="col-12 col-lg-6">
				<fieldset id="fieldset-detail" class="options-form">
					<legend><?php echo Text::_('COM_SERMONSPEAKER_DETAIL'); ?></legend>
					<div>
						<?php echo $this->form->renderFieldset('detail'); ?>
					</div>
				</fieldset>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
			<div class="row">
				<div class="col-12 col-lg-6">
					<fieldset id="fieldset-publishingdata" class="options-form">
						<legend><?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
						<div>
							<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
						</div>
					</fieldset>
				</div>
				<div class="col-12 col-lg-6">
					<fieldset id="fieldset-metadata" class="options-form">
						<legend><?php echo Text::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'); ?></legend>
						<div>
							<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
						</div>
					</fieldset>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php if (Associations::isEnabled()) : ?>
			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'associations', Text::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
				<?php echo LayoutHelper::render('joomla.edit.associations', $this); ?>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>
		<?php endif; ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $jinput->getCmd('return');?>" />
		<input type="hidden" name="forcedLanguage" value="<?php echo $jinput->get('forcedLanguage', '', 'cmd'); ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
