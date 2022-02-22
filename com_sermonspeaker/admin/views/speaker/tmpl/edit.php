<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// Include the component HTML helpers.
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_contenthistory');
$wa->useScript('keepalive')
	->useScript('form.validate')
	->useScript('com_contenthistory.admin-history-versions');

$this->ignore_fieldsets = array('general', 'info', 'detail', 'jmetadata', 'item_associations');
$this->useCoreUI        = true;

$jinput = Factory::getApplication()->input;

// In case of modal
$isModal = $jinput->get('layout') == 'modal';
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $jinput->get('tmpl', '') === 'component' ? '&tmpl=component' : '';
?>
<form action="<?php echo Route::_('index.php?option=com_sermonspeaker&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>"
	  method="post" name="adminForm" id="item-form" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_SERMONSPEAKER_FIELD_BIO_LABEL', true)); ?>
		<div class="row">
			<div class="col-12 col-lg-9">
				<fieldset class="options-form">
					<legend><?php echo Text::_('COM_SERMONSPEAKER_FIELD_INTRO_LABEL'); ?></legend>
					<?php echo $this->form->getInput('intro'); ?>
				</fieldset>
				<fieldset class="options-form">
					<legend><?php echo Text::_('COM_SERMONSPEAKER_FIELD_BIO_LABEL'); ?></legend>
					<?php echo $this->form->getInput('bio'); ?>
				</fieldset>
			</div>
			<div class="col-12 col-lg-3">
				<div class="bg-white px-3">
					<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('JDETAILS', true)); ?>
		<div class="col-12 col-lg-6">
			<fieldset id="fieldset-detail" class="options-form">
				<legend><?php echo Text::_('COM_SERMONSPEAKER_DETAIL'); ?></legend>
				<?php foreach ($this->form->getFieldset('detail') as $field): ?>
					<?php echo $field->renderField(); ?>
				<?php endforeach; ?>
			</fieldset>
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
			<?php if (!$isModal) : ?>
				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'associations', Text::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
				<?php echo LayoutHelper::render('joomla.edit.associations', $this); ?>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php else : ?>
				<div class="hidden"><?php echo LayoutHelper::render('joomla.edit.associations', $this); ?></div>
			<?php endif; ?>
		<?php endif; ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="return" value="<?php echo $jinput->getCmd('return'); ?>"/>
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
