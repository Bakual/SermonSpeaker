<?php
// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('bootstrap.tooltip');
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

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general'));

		echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_SERMONSPEAKER_FIELD_BIO_LABEL', true)); ?>
			<div class="row-fluid">
				<div class="span9">
					<fieldset class="adminform">
						<?php echo $this->form->getLabel('intro'); ?>
						<?php echo $this->form->getInput('intro'); ?>
						<?php echo $this->form->getLabel('bio'); ?>
						<?php echo $this->form->getInput('bio'); ?>
					</fieldset>
				</div>
				<div class="span3">
					<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('JDETAILS', true)); ?>
			<?php foreach($this->form->getFieldset('detail') as $field): ?>
				<?php echo $field->getControlGroup(); ?>
			<?php endforeach; ?>
		<?php echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab');
		if (JLanguageAssociations::isEnabled()) :
			echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true));
			echo $this->loadTemplate('associations');
			echo JHtml::_('bootstrap.endTab');
		endif;
		echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>