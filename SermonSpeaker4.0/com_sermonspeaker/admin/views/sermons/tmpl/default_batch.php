<?php
// no direct access
defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_SERMONSPEAKER_BATCH_OPTIONS');?></legend>
	<p><?php echo JText::_('COM_SERMONSPEAKER_BATCH_TIP'); ?></p>
	<?php // echo JHtml::_('batch.language');?>
	<label id="batch-speaker-lbl" for="batch-speaker-id" class="hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_BATCH_SPEAKER_LABEL'); ?>::<?php echo JText::_('COM_SERMONSPEAKER_BATCH_SPEAKER_LABEL_DESC'); ?>">
		<?php echo JText::_('COM_SERMONSPEAKER_BATCH_SPEAKER_LABEL'); ?>
	</label>
	<select name="batch[speaker_id]" class="inputbox" id="batch-speaker-id">
		<option value=""><?php echo JText::_('COM_SERMONSPEAKER_BATCH_SPEAKER_NOCHANGE'); ?></option>
		<?php echo JHtml::_('select.options', $this->speakers, 'value', 'text'); ?>
	</select>
	<label id="batch-serie-lbl" for="batch-serie-id" class="hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_BATCH_SERIE_LABEL'); ?>::<?php echo JText::_('COM_SERMONSPEAKER_BATCH_SERIE_LABEL_DESC'); ?>">
		<?php echo JText::_('COM_SERMONSPEAKER_BATCH_SERIE_LABEL'); ?>
	</label>
	<select name="batch[serie_id]" class="inputbox" id="batch-serie-id">
		<option value=""><?php echo JText::_('COM_SERMONSPEAKER_BATCH_SERIE_NOCHANGE'); ?></option>
		<?php echo JHtml::_('select.options', $this->series, 'value', 'text'); ?>
	</select>

	<?php if ($published >= 0) : ?>
		<?php echo JHtml::_('batch.item', 'com_sermonspeaker');?>
	<?php endif; ?>

	<button type="submit" onclick="Joomla.submitbutton('sermon.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-speaker-id').value='';document.id('batch-serie-id').value=''">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>
