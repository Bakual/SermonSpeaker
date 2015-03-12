<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
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
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_SERMONSPEAKER_PUBLISHING') ?></a></li>
				<li><a href="#language" data-toggle="tab"><?php echo JText::_('JFIELD_LANGUAGE_LABEL') ?></a></li>
				<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('COM_SERMONSPEAKER_METADATA') ?></a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="editor">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('title'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('title'); ?>
						</div>
					</div>

					<?php if (is_null($this->item->id)): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('alias'); ?>
							</div>
						</div>
					<?php endif; ?>

					<?php echo $this->form->getInput('series_description'); ?>
				</div>
				<div class="tab-pane" id="details">
					<?php foreach($this->form->getFieldset('detail') as $field): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="tab-pane" id="publishing">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('catid'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('catid'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('tags', 'metadata'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('tags', 'metadata'); ?>
						</div>
					</div>
					<?php if ($this->user->authorise('core.edit.state', 'com_sermonspeaker')): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('state'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('state'); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="tab-pane" id="language">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('language'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('language'); ?>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="metadata">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('metadesc'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('metadesc'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('metakey'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('metakey'); ?>
						</div>
					</div>
				</div>			</div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
</div>
