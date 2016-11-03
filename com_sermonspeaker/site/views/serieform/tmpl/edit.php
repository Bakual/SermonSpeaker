<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'serieform.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('series_description')->save(); ?>
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=serieform&s_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form form-vertical">
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('serieform.save')">
					<i class="icon-ok"></i> <?php echo JText::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('serieform.cancel')">
					<i class="icon-cancel"></i> <?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
		</div>
		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('JEDITOR') ?></a></li>
				<li><a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS') ?></a></li>
				<?php foreach ($this->form->getFieldsets('params') as $name => $fieldSet) : ?>
					<li><a href="#params-<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_($fieldSet->label) ?></a></li>
				<?php endforeach; ?>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_SERMONSPEAKER_PUBLISHING') ?></a></li>
				<li><a href="#language" data-toggle="tab"><?php echo JText::_('JFIELD_LANGUAGE_LABEL') ?></a></li>
				<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('COM_SERMONSPEAKER_METADATA') ?></a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="editor">
					<?php echo $this->form->renderField('title'); ?>

					<?php if (is_null($this->item->id)): ?>
						<?php echo $this->form->renderField('alias'); ?>
					<?php endif; ?>

					<?php echo $this->form->getInput('series_description'); ?>
				</div>
				<div class="tab-pane" id="details">
					<?php foreach($this->form->getFieldset('detail') as $field): ?>
						<?php echo $this->form->renderField($field->fieldname); ?>
					<?php endforeach; ?>
				</div>
				<?php foreach ($this->form->getFieldsets('params') as $name => $fieldSet) : ?>
					<div class="tab-pane" id="params-<?php echo $name; ?>">
						<?php if (isset($fieldSet->description) && trim($fieldSet->description)): ?>
							<?php echo '<p class="alert alert-info">' . $this->escape(JText::_($fieldSet->description)) . '</p>'; ?>
						<?php endif; ?>
						<?php foreach ($this->form->getFieldset($name) as $field) : ?>
							<?php echo $field->renderField(); ?>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
				<div class="tab-pane" id="publishing">
					<?php echo $this->form->renderField('catid'); ?>
					<?php echo $this->form->renderField('tags'); ?>
					<?php if ($this->user->authorise('core.edit.state', 'com_sermonspeaker')): ?>
						<?php echo $this->form->renderField('state'); ?>
						<?php echo $this->form->renderField('publish_up'); ?>
						<?php echo $this->form->renderField('publish_down'); ?>
					<?php endif; ?>
				</div>
				<div class="tab-pane" id="language">
					<?php echo $this->form->renderField('language'); ?>
				</div>
				<div class="tab-pane" id="metadata">
					<?php echo $this->form->renderField('metadesc'); ?>
					<?php echo $this->form->renderField('metakey'); ?>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
</div>
