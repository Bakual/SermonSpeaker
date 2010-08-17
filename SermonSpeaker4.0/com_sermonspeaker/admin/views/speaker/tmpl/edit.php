<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	function submitbutton(task)
	{
		if (task == 'speaker.cancel' || document.formvalidator.isValid(document.id('speaker-form'))) {
			<?php echo $this->form->getField('intro')->save(); ?>
			<?php echo $this->form->getField('bio')->save(); ?>
			submitform(task);
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php JRoute::_('index.php?option=com_sermonspeaker'); ?>" method="post" name="adminForm" id="speaker-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_SERMONSPEAKER_NEW_SPEAKER') : JText::sprintf('COM_SERMONSPEAKER_EDIT_SPEAKER', $this->item->id); ?></legend>
			<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?></li>

			<li><?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?></li>

			<li><?php echo $this->form->getLabel('catid'); ?>
			<?php echo $this->form->getInput('catid'); ?></li>

			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>

			<li><?php echo $this->form->getLabel('pic'); ?>
			<?php echo $this->form->getInput('pic'); ?></li>
			</ul>

			<li><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>
			</ul>

			<?php echo $this->form->getLabel('intro'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('intro'); ?>

			<?php echo $this->form->getLabel('bio'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('bio'); ?>

		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start','newsfeed-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

		<?php echo JHtml::_('sliders.panel',JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>

		<fieldset class="panelform">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('created_by'); ?>
				<?php echo $this->form->getInput('created_by'); ?></li>

				<li><?php echo $this->form->getLabel('created_on'); ?>
				<?php echo $this->form->getInput('created_on'); ?></li>

				<li><?php echo $this->form->getLabel('hits'); ?>
				<?php echo $this->form->getInput('hits'); ?></li>
			</ul>
		</fieldset>

		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<div class="clr"></div>
</form>