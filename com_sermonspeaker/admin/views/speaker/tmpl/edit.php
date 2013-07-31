<?php
// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$app	= JFactory::getApplication();
$input	= $app->input;
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'speaker.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('intro')->save(); ?>
			<?php echo $this->form->getField('bio')->save(); ?>
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general'));
				echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('JDETAILS', true)); ?>
					<fieldset class="adminform">
						<div class="control-group form-inline">
							<?php echo $this->form->getLabel('title'); ?> <?php echo $this->form->getInput('title'); ?> <?php echo $this->form->getLabel('catid'); ?> <?php echo $this->form->getInput('catid'); ?>
						</div>
						<?php echo $this->form->getInput('intro'); ?>
						<?php echo $this->form->getInput('bio'); ?>
					</fieldset>
					<div class="row-fluid">
						<div class="span6">
							<h4><?php echo JText::_('JDETAILS');?></h4>
							<?php foreach($this->form->getFieldset('detail') as $field): ?>
								<div class="control-group">
									<?php if (!$field->hidden): ?>
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
									<?php endif; ?>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php echo JHtml::_('bootstrap.endTab');
				echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
					<div class="row-fluid">
						<div class="span6">
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('alias'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('alias'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('id'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('id'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('created_by'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('created_by'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('created'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('created'); ?>
								</div>
							</div>
						</div>
						<div class="span6">
							<?php if ($this->item->modified_by) : ?>
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('modified_by'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('modified_by'); ?>
									</div>
								</div>
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('modified'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('modified'); ?>
									</div>
								</div>
							<?php endif; ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('hits'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('hits'); ?>
								</div>
							</div>
						</div>
					</div>
				<?php echo JHtml::_('bootstrap.endTab');
				echo JHtml::_('bootstrap.addTab', 'myTab', 'metadata', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS', true));
					echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
				<?php echo JHtml::_('bootstrap.endTab');
				if (!empty($app->item_associations)) :
					echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true));
						echo $this->loadTemplate('associations');
					echo JHtml::_('bootstrap.endTab');
				endif;
			echo JHtml::_('bootstrap.endTabSet'); ?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
		<!-- End Content -->
		<!-- Begin Sidebar -->
			<?php echo JLayoutHelper::render('joomla.edit.details', $this); ?>
		<!-- End Sidebar -->
	</div>
</form>