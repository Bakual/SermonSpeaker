<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('stylesheet', 'com_sermonspeaker/frontendupload.css', array('relative' => true));
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.tabstate');
HTMLHelper::_('behavior.calendar');
HTMLHelper::_('behavior.modal');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('formbehavior.chosen', 'select');

$this->ignore_fieldsets = array('general', 'files', 'info', 'detail', 'publishingdata', 'jmetadata', 'metadata', 'item_associations');
$this->tab_name = 'sermonEditTab';

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
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('frontendupload.save')">
					<i class="icon-ok"></i> <?php echo Text::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('frontendupload.cancel')">
					<i class="icon-cancel"></i> <?php echo Text::_('JCANCEL') ?>
				</button>
			</div>
		</div>
        <fieldset>
            <?php echo HtmlHelper::_('bootstrap.startTabSet', $this->tab_name, array('active' => 'editor')); ?>

            <?php echo HtmlHelper::_('bootstrap.addTab', $this->tab_name, 'editor', Text::_('JEDITOR', true)); ?>
                <?php echo $this->form->renderField('title'); ?>

                <?php if (is_null($this->item->id)): ?>
                    <?php echo $this->form->renderField('alias'); ?>
                <?php endif;

                echo $this->form->getInput('notes'); ?>
            <?php echo HtmlHelper::_('bootstrap.endTab'); ?>

            <?php echo HtmlHelper::_('bootstrap.addTab', $this->tab_name, 'files', Text::_('COM_SERMONSPEAKER_FU_FILES', true)); ?>
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
                            <div id="audiopathinfo" class="label label-info hasTooltip"
                                 title="<?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
                                <?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO');

                                if ($this->s3audio) :
                                    echo ' https://' . $this->domain . '/';
                                else :
                                    echo ' /' . trim($this->params->get('path_audio'), '/') . '/';
                                endif;
                                echo '<span id="audiopathdate" class="pathdate">' . $this->append_date . '</span><span id="audiopathlang" class="pathlang">' . $this->append_lang . '</span>'; ?>
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
                            <div id="videopathinfo" class="label label-info hasTooltip"
                                 title="<?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
                                <?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO');

                                if ($this->s3video):
                                    echo ' https://' . $this->domain . '/';
                                else:
                                    echo ' /' . trim($this->params->get('path_video'), '/') . '/';
                                endif;
                                echo '<span id="videopathdate" class="pathdate">' . $this->append_date . '</span><span id="videopathlang" class="pathlang">' . $this->append_lang . '</span>'; ?>
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
                            <div id="addfilepathinfo" class="label label-info hasTooltip"
                                 title="<?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO_TOOLTIP'); ?>">
                                <?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO') . ' /' . trim($this->params->get('path_addfile'), '/')
                                    . '/<span id="addfilepathdate" class="pathdate">' . $this->append_date . '</span>'
                                    . '<span id="addfilepathlang" class="pathlang">' . $this->append_lang . '</span>'; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php echo $this->form->renderField('addfileDesc'); ?>
            <?php echo HtmlHelper::_('bootstrap.endTab'); ?>

            <?php echo HtmlHelper::_('bootstrap.addTab', $this->tab_name, 'details', Text::_('JDETAILS', true)); ?>
                <?php foreach ($this->form->getFieldset('detail') as $field): ?>
                    <?php echo $this->form->renderField($field->fieldname); ?>
                <?php endforeach; ?>
            <?php echo HtmlHelper::_('bootstrap.endTab'); ?>

            <?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

            <?php echo HtmlHelper::_('bootstrap.addTab', $this->tab_name, 'publishing', Text::_('COM_SERMONSPEAKER_PUBLISHING', true)); ?>
                <?php echo $this->form->renderField('catid'); ?>
                <?php echo $this->form->renderField('tags'); ?>
                <?php if ($this->user->authorise('core.edit.state', 'com_sermonspeaker')): ?>
                    <?php echo $this->form->renderField('state'); ?>
                    <?php echo $this->form->renderField('podcast'); ?>
                    <?php echo $this->form->renderField('publish_up'); ?>
                    <?php echo $this->form->renderField('publish_down'); ?>
                <?php endif; ?>
            <?php echo HtmlHelper::_('bootstrap.endTab'); ?>

            <?php echo HtmlHelper::_('bootstrap.addTab', $this->tab_name, 'language', Text::_('JFIELD_LANGUAGE_LABEL', true)); ?>
                <?php echo $this->form->renderField('language'); ?>
            <?php echo HtmlHelper::_('bootstrap.endTab'); ?>

            <?php echo HtmlHelper::_('bootstrap.addTab', $this->tab_name, 'metadata', Text::_('COM_SERMONSPEAKER_METADATA', true)); ?>
                <?php echo $this->form->renderField('metadesc'); ?>
                <?php echo $this->form->renderField('metakey'); ?>
            <?php echo HtmlHelper::_('bootstrap.endTab'); ?>

            <?php echo HtmlHelper::_('bootstrap.endTabSet'); ?>

            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="return" value="<?php echo $this->return_page; ?>"/>
            <?php echo HtmlHelper::_('form.token'); ?>
        </fieldset>
	</form>
	<?php if ($this->params->get('enable_non_flash')) : ?>
		<div id="upload-noflash">
			<form
				action="<?php echo Uri::root(); ?>index.php?option=com_sermonspeaker&amp;task=file.upload&amp;tmpl=component&amp;<?php echo Factory::getSession()->getName() . '=' . Factory::getSession()->getId(); ?>&amp;<?php echo JSession::getFormToken(); ?>=1"
				id="uploadForm" name="uploadForm" class="form-validate form form-vertical" method="post"
				enctype="multipart/form-data">
                <fieldset>
                    <legend><?php echo Text::_('COM_SERMONSPEAKER_FU_SELECTFILE'); ?></legend>
                    <div class="control-group">
                        <div class="control-label">
                            <label
                                    for="upload-audiofile"><?php echo Text::_('COM_SERMONSPEAKER_FIELD_AUDIOFILE_LABEL'); ?></label>
                        </div>
                        <div class="controls">
                            <input type="file" size="50" id="upload-audiofile" name="Filedata[]"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label
                                    for="upload-videofile"><?php echo Text::_('COM_SERMONSPEAKER_FIELD_VIDEOFILE_LABEL'); ?></label>
                        </div>
                        <div class="controls">
                            <input type="file" size="50" id="upload-videofile" name="Filedata[]"/>
                        </div>
                    </div>
                    <div class="well well-small">
                        <div><?php echo Text::_('COM_SERMONSPEAKER_UPLOADINFO'); ?>
                            <span
                                    class="label label-info">/<?php echo trim($this->params->get('path_audio', $this->params->get('path')), '/') . '/';

				                if ($this->params->get('append_path', 0)) :
					                $time = ($this->item->sermon_date AND $this->item->sermon_date != '0000-00-00 00:00:00') ? strtotime($this->item->sermon_date) : time();
					                ?><input type="text" id="year" size="4" name="year"
                                             value="<?php echo date('Y', $time); ?>" />/<input type="text" id="month" size="2"
                                                                                               name="month" value="<?php echo date('m', $time); ?>"/>/<?php
				                endif;

				                if ($this->params->get('append_path_lang', 0)) :
					                $lang = $this->item->language;

					                if (!$lang || $lang == '*') :
						                $lang = Factory::getLanguage()->getTag();
					                endif;
					                ?><input type="text" id="lang" size="5" name="lang" value="<?php echo $lang; ?>" />/
					<?php endif; ?></span>.
                        </div>
                        <div><?php echo Text::sprintf('COM_SERMONSPEAKER_UPLOAD_LIMIT', $this->upload_limit); ?></div>
                    </div>
                    <button type="submit" class="btn">
                        <i class="icon-upload"></i> <?php echo Text::_('COM_SERMONSPEAKER_FU_START_UPLOAD'); ?>
                    </button>
                    <input type="hidden" name="return-url" value="<?php echo base64_encode($self); ?>"/>
                </fieldset>
			</form>
		</div>
	<?php endif; ?>
</div>
