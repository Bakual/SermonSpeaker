<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

// @deprecated 4.0 the function parameter, the inline js and the buttons are not needed since 3.7.0.
$jinput   = JFactory::getApplication()->input;
$function = $jinput->getCmd('function', 'jEditSpeaker_' . (int) $this->item->id);

// Function to update input title when changed
JFactory::getDocument()->addScriptDeclaration('
	function jEditSpeakerModal() {
		if (window.parent && document.formvalidator.isValid(document.getElementById("adminForm"))) {
			return window.parent.' . $this->escape($function) . '(document.getElementById("jform_title").value);
		}
	}
');
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
	<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=speakerform&modal=1&s_id=' . (int) $this->item->id); ?>"
		  method="post" name="adminForm" id="adminForm" class="form-validate form form-vertical">
		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('JEDITOR') ?></a></li>
				<li><a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS') ?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_SERMONSPEAKER_PUBLISHING') ?></a>
				</li>
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

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('intro'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('intro'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('bio'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('bio'); ?>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="details">
					<?php foreach ($this->form->getFieldset('detail') as $field): ?>
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
				</div>
			</div>
			<input type="hidden" id="jform_id" value="<?php echo $this->form->getValue('id'); ?>"/>
			<input type="hidden" name="layout" value="modal"/>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="return" value="<?php echo $jinput->getCmd('return'); ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
</div>
