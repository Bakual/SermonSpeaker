<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

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

HTMLHelper::_('script', 'com_contenthistory/admin-history-versions.js', ['version' => 'auto', 'relative' => true]);

$this->ignore_fieldsets = array('general', 'info', 'detail', 'jmetadata', 'item_associations');
$this->useCoreUI = true;

$jinput = Factory::getApplication()->input;

// In case of modal
$isModal = $jinput->get('layout') == 'modal' ? true : false;
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $jinput->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('JGLOBAL_FIELDSET_CONTENT', true)); ?>
			<div class="row">
				<div class="col-lg-9">
					<fieldset class="adminform">
						<?php echo $this->form->getInput('series_description'); ?>
					</fieldset>
				</div>
				<div class="col-lg-3">
					<div class="card card-body card-light">
						<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
					</div>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('JDETAILS', true)); ?>
			<?php foreach($this->form->getFieldset('detail') as $field): ?>
				<?php echo $field->renderField(); ?>
			<?php endforeach; ?>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
			<div class="row">
				<div class="col-lg-6">
					<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
				<div class="col-lg-6">
					<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
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
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $jinput->getCmd('return'); ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>