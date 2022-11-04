<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));

$this->ignore_fieldsets = array('general', 'info', 'detail', 'jmetadata', 'metadata', 'item_associations');
$this->tab_name         = 'speakerEditTab';
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
			action="<?php echo Route::_('index.php?option=com_sermonspeaker&view=speakerform&modal=1&s_id=' . (int) $this->item->id); ?>"
			method="post" name="adminForm" id="item-form" class="form-validate form form-vertical">
		<form action="<?php echo Route::_('index.php?option=com_sermonspeaker&view=speakerform&modal=1&s_id=' . (int) $this->item->id); ?>"
			  method="post" name="adminForm" id="adminForm" class="form-validate form form-vertical">
			<fieldset>
				<?php echo HTMLHelper::_('bootstrap.startTabSet', $this->tab_name, array('active' => 'editor')); ?>

				<?php echo HTMLHelper::_('bootstrap.addTab', $this->tab_name, 'editor', Text::_('JEDITOR', true)); ?>
					<?php echo $this->form->renderField('title'); ?>

					<?php if (is_null($this->item->id)): ?>
						<?php echo $this->form->renderField('alias'); ?>
					<?php endif; ?>

					<?php echo $this->form->renderField('intro'); ?>
					<?php echo $this->form->renderField('bio'); ?>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

				<?php echo HTMLHelper::_('bootstrap.addTab', $this->tab_name, 'details', Text::_('JDETAILS', true)); ?>
					<?php foreach ($this->form->getFieldset('detail') as $field): ?>
						<?php echo $this->form->renderField($field->fieldname); ?>
					<?php endforeach; ?>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

				<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

				<?php echo HTMLHelper::_('bootstrap.addTab', $this->tab_name, 'publishing', Text::_('COM_SERMONSPEAKER_PUBLISHING', true)); ?>
					<?php echo $this->form->renderField('catid'); ?>
					<?php if ($this->user->authorise('core.edit.state', 'com_sermonspeaker')): ?>
						<?php echo $this->form->renderField('state'); ?>
					<?php endif; ?>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

				<?php echo HTMLHelper::_('bootstrap.addTab', $this->tab_name, 'language', Text::_('JFIELD_LANGUAGE_LABEL', true)); ?>
					<?php echo $this->form->renderField('language'); ?>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

				<?php echo HTMLHelper::_('bootstrap.addTab', $this->tab_name, 'metadata', Text::_('COM_SERMONSPEAKER_METADATA', true)); ?>
					<?php echo $this->form->renderField('metadesc'); ?>
					<?php echo $this->form->renderField('metakey'); ?>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

				<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>

				<input type="hidden" name="layout" value="modal"/>
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="return" value="<?php echo $this->return_page; ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>
			</fieldset>
		</form>
</div>
