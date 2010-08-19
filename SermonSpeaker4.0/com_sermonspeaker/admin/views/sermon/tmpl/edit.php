<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	function submitbutton(task)
	{
		if (task == 'sermon.cancel' || document.formvalidator.isValid(document.id('sermon-form'))) {
			<?php echo $this->form->getField('notes')->save(); ?>
			submitform(task);
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php JRoute::_('index.php?option=com_sermonspeaker'); ?>" method="post" name="adminForm" id="sermon-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_SERMONSPEAKER_NEW_SERMON') : JText::sprintf('COM_SERMONSPEAKER_EDIT_SERMON', $this->item->id); ?></legend>
			<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('sermon_title'); ?>
			<?php echo $this->form->getInput('sermon_title'); ?></li>

			<li><?php echo $this->form->getLabel('alias'); ?>
			<?php echo $this->form->getInput('alias'); ?></li>

			<li><?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?></li>

			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>
			</ul>

			<?php echo $this->form->getLabel('notes'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('notes'); ?>

		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<fieldset class="adminform" style="border: 1px dashed silver; padding: 5px; margin: 18px 0px 10px;">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>

				<li><?php echo $this->form->getLabel('created_by'); ?>
				<?php echo $this->form->getInput('created_by'); ?></li>

				<li><?php echo $this->form->getLabel('created_on'); ?>
				<?php echo $this->form->getInput('created_on'); ?></li>

				<li><?php echo $this->form->getLabel('hits'); ?>
				<?php echo $this->form->getInput('hits'); ?></li>
			</ul>
		</fieldset>
		<?php echo JHtml::_('sliders.start','sermon-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
		<?php echo JHtml::_('sliders.panel',JText::_('COM_SERMONSPEAKER_GENERAL'), 'general-panel'); ?>
		<fieldset class="panelform">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('sermon_number'); ?>
				<?php echo $this->form->getInput('sermon_number'); ?></li>

				<li><?php echo $this->form->getLabel('sermon_date'); ?>
				<?php echo $this->form->getInput('sermon_date'); ?></li>

				<li><?php echo $this->form->getLabel('sermon_time'); ?>
				<?php echo $this->form->getInput('sermon_time'); ?></li>

				<li><?php echo $this->form->getLabel('speaker'); ?>
				<?php echo $this->speakers; ?></li>

				<li><?php echo $this->form->getLabel('series'); ?>
				<?php echo $this->series; ?></li>

				<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?></li>
			</ul>
		</fieldset>

		<?php echo JHtml::_('sliders.panel',JText::_('COM_SERMONSPEAKER_CUSTOM'), 'custom-panel'); ?>
		<fieldset class="panelform">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('custom1'); ?>
				<?php echo $this->form->getInput('custom1'); ?></li>

				<li><?php echo $this->form->getLabel('custom2'); ?>
				<?php echo $this->form->getInput('custom2'); ?></li>
			</ul>
		</fieldset>

		<?php echo JHtml::_('sliders.panel',JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-panel'); ?>
		<fieldset class="panelform">
			<?php echo $this->form->getLabel('metadesc'); ?>
			<?php echo $this->form->getInput('metadesc'); ?>

			<?php echo $this->form->getLabel('metakey'); ?>
			<?php echo $this->form->getInput('metakey'); ?>
		</fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<div class="clr"></div>
</form>