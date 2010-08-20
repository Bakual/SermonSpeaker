<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

if(empty($this->item->id)){
	$self = JURI::current().'?option=com_sermonspeaker&controller=sermon&task=add';
} else {
	$uri = JURI::getInstance();
	$self = $uri->toString();
}
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

			<li><?php echo $this->form->getLabel('podcast'); ?>
			<?php echo $this->form->getInput('podcast'); ?></li>

			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>
			</ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_SERMONPATH'); ?></legend>
			<input type="radio" name="sel" value="1" onclick="enableElement(this.form.elements['sermon_path_txt'], this.form.elements['sermon_path_choice']);" checked>
			<input class="text_area" type="text" name="sermon_path_txt" id="sermon_path_txt" size="100" maxlength="250" value="<?php echo $this->item->sermon_path;?>" />
			<div class="clr"></div>
			<?php echo $this->form->getLabel(''); ?>
			<input type="radio" name="sel" value="2" onclick="enableElement(this.form.elements['sermon_path_choice'], this.form.elements['sermon_path_txt']);">
			<?php echo $this->sermon_files; ?>
			<img onClick="window.location.href='<?php echo $self; ?>&amp;file='+document.adminForm.sermon_path_choice.value;" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/find.png' alt='lookup ID3' title='lookup ID3'>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?></legend>
			<div class="clr"></div>
			<?php echo $this->form->getInput('notes'); ?>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></legend>
			<input type="radio" name="seladdfile" value="1" onclick="enableElement(this.form.elements['addfile_txt'], this.form.elements['addfile_choice']);" checked>
			<input class="text_area" type="text" name="addfile_txt" id="addfile_txt" size="100" maxlength="250" value="<?php echo $this->item->addfile;?>" />
			<div class="clr"></div>
			<input type="radio" name="seladdfile" value="2" onclick="enableElement(this.form.elements['addfile_choice'], this.form.elements['addfile_txt']);">
			<?php echo $this->addfiles; ?>
			<ul>
			<li><?php echo $this->form->getLabel('addfiledesc'); ?>
			<?php echo $this->form->getInput('addfiledesc'); ?></li>
			</ul>
		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<fieldset class="adminform" style="border: 1px dashed silver; padding: 5px; margin: 18px 0px 10px;">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>

				<li><?php echo $this->form->getLabel('created_by'); ?>
				<?php echo $this->form->getInput('created_by'); ?></li>

				<li><?php echo $this->form->getLabel('created'); ?>
				<?php echo $this->form->getInput('created'); ?></li>

				<li><?php echo $this->form->getLabel('hits'); ?>
				<?php echo $this->form->getInput('hits'); ?>
				<?php if ($this->item->hits) { ?>
					<a href="index.php?option=com_sermonspeaker&task=sermon.resetcount&id=<?php echo $this->item->id; ?>">
						<img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/reset.png" width="16" height="16" border="0" title="<?php echo JText::_('JSEARCH_RESET'); ?>" alt="Reset" />
					</a>
				<?php } ?></li>

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

				<li><?php echo $this->form->getLabel('sermon_scripture'); ?>
				<?php echo $this->form->getInput('sermon_scripture'); ?>
				<img onClick="sendText(document.adminForm.jform_sermon_scripture,'{bib=','}')" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/blue_tag.png' title='insert Biblelink tag' alt='insert Biblelink tag'>
				<img onClick="sendText(document.adminForm.jform_sermon_scripture,'{bible}','{/bible}')" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/green_tag.png' title='insert ScriptureLink tag' alt='insert ScriptureLink tag'>
				</li>

				<li><?php echo $this->form->getLabel('speaker_id'); ?>
				<?php echo $this->speakers; ?></li>

				<li><?php echo $this->form->getLabel('series_id'); ?>
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