<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('keepalive');
$wa->useScript('form.validate');

HTMLHelper::_('stylesheet', 'com_sermonspeaker/frontendupload.css', array('relative' => true));
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

// TODO:Replace HTMLHelper::_('uitab.modal') with uitab equivalent.

$this->ignore_fieldsets = array('general', 'files', 'info', 'detail', 'publishingdata', 'jmetadata', 'metadata', 'item_associations');
$this->tab_name         = 'sermonEditTab';
$this->useCoreUI        = true;

$uri = Uri::getInstance();
$uri->delVar('file');
$uri->delVar('file0');
$uri->delVar('file1');
$uri->delVar('type');
$self = $uri->toString();
?>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>

	<form
			action="<?php echo Route::_('index.php?option=com_sermonspeaker&view=frontendupload&s_id=' . (int) $this->item->id); ?>"
			method="post" name="adminForm" id="adminForm" class="form-validate form form-vertical">
		<fieldset>
			<?php echo HTMLHelper::_('uitab.startTabSet', $this->tab_name, array('active' => 'editor')); ?>

			<?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'editor', Text::_('JEDITOR', true)); ?>
			<?php echo $this->form->renderField('title'); ?>

			<?php if (is_null($this->item->id)): ?>
				<?php echo $this->form->renderField('alias'); ?>
			<?php endif; ?>

			<?php echo $this->form->renderField('notes'); ?>
			<br>
			<?php echo $this->form->renderField('maintext'); ?>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'files', Text::_('COM_SERMONSPEAKER_FU_FILES', true)); ?>
			<div id="upload_limit" class="well well-small ss-hide">
				<?php echo Text::sprintf('COM_SERMONSPEAKER_UPLOAD_LIMIT', $this->upload_limit); ?>
			</div>
			<div id="audiofile_drop" class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('audiofile'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('audiofile');

					if ($this->params->get('enable_flash')) : ?>
						<div id="audiopathinfo" class="badge bg-info hasTooltip"
							 title="<?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
							<?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO');

							if ($this->s3audio) :
								echo ' https://' . $this->domain . '/';
							else :
								echo ' /' . trim($this->params->get('path_audio'), '/') . '/';
							endif;
							echo $this->append_user . '<span id="audiopathdate" class="pathdate">' . $this->append_date . '</span><span id="audiopathlang" class="pathlang">' . $this->append_lang . '</span>'; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<hr/>
			<div id="videofile_drop" class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('videofile'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('videofile');

					if ($this->params->get('enable_flash')) : ?>
						<div id="videopathinfo" class="badge bg-info hasTooltip"
							 title="<?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
							<?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO');

							if ($this->s3video):
								echo ' https://' . $this->domain . '/';
							else:
								echo ' /' . trim($this->params->get('path_video'), '/') . '/';
							endif;
							echo $this->append_user . '<span id="videopathdate" class="pathdate">' . $this->append_date . '</span><span id="videopathlang" class="pathlang">' . $this->append_lang . '</span>'; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<hr/>
			<div id="addfile_drop" class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('addfile'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('addfile');

					if ($this->params->get('enable_flash')) : ?>
						<div id="addfilepathinfo" class="badge bg-info hasTooltip"
							 title="<?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
							<?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO') . ' /' . trim($this->params->get('path_addfile'), '/')
								. '/' . $this->append_user . '<span id="addfilepathdate" class="pathdate">' . $this->append_date . '</span>'
								. '<span id="addfilepathlang" class="pathlang">' . $this->append_lang . '</span>'; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php echo $this->form->renderField('addfileDesc'); ?>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'details', Text::_('JDETAILS', true)); ?>
			<?php foreach ($this->form->getFieldset('detail') as $field): ?>
				<?php echo $this->form->renderField($field->fieldname); ?>
			<?php endforeach; ?>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

			<?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'publishing', Text::_('COM_SERMONSPEAKER_PUBLISHING', true)); ?>
			<?php echo $this->form->renderField('catid'); ?>
			<?php echo $this->form->renderField('tags'); ?>
			<?php if ($this->user->authorise('core.edit.state', 'com_sermonspeaker')): ?>
				<?php echo $this->form->renderField('state'); ?>
				<?php echo $this->form->renderField('podcast'); ?>
				<?php echo $this->form->renderField('publish_up'); ?>
				<?php echo $this->form->renderField('publish_down'); ?>
			<?php endif; ?>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'language', Text::_('JFIELD_LANGUAGE_LABEL', true)); ?>
			<?php echo $this->form->renderField('language'); ?>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'metadata', Text::_('COM_SERMONSPEAKER_METADATA', true)); ?>
			<?php echo $this->form->renderField('metadesc'); ?>
			<?php echo $this->form->renderField('metakey'); ?>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</fieldset>
		<div class="mb-2">
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('frontendupload.save')">
				<span class="icon-check" aria-hidden="true"></span>
				<?php echo Text::_('JSAVE') ?>
			</button>
			<button type="button" class="btn btn-danger" onclick="Joomla.submitbutton('frontendupload.cancel')">
				<span class="icon-times" aria-hidden="true"></span>
				<?php echo Text::_('JCANCEL') ?>
			</button>
			<?php if ($this->params->get('save_history', 0) && $this->item->id) : ?>
				<?php echo $this->form->getInput('contenthistory'); ?>
			<?php endif; ?>
		</div>
	</form>
</div>
