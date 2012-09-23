<?php
// no direct access
defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_SERMONSPEAKER_BATCH_OPTIONS');?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo JText::_('COM_SERMONSPEAKER_BATCH_TIP'); ?></p>
		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('batch.language'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
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
			</div>
		</div>
		<?php if ($published >= 0) : ?>
		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('batch.item', 'com_sermonspeaker');?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-category-id').value='';document.id('batch-language-id').value=''" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('sermon.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>



