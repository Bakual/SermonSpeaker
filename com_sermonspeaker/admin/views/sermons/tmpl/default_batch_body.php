<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="control-group span6">
			<div class="controls">
				<label id="batch-speaker-lbl" for="batch-speaker-id" class="modalTooltip"
					   title="<?php echo JHtml::tooltipText('COM_SERMONSPEAKER_BATCH_SPEAKER_LABEL', 'COM_SERMONSPEAKER_BATCH_SPEAKER_LABEL_DESC'); ?>">
					<?php echo JText::_('COM_SERMONSPEAKER_BATCH_SPEAKER_LABEL'); ?>
				</label>
				<select name="batch[speaker_id]" class="inputbox" id="batch-speaker-id">
					<option value=""><?php echo JText::_('COM_SERMONSPEAKER_BATCH_SPEAKER_NOCHANGE'); ?></option>
					<?php echo JHtml::_('select.options', $this->speakers, 'value', 'text'); ?>
				</select>
			</div>
		</div>
		<div class="control-group span6">
			<div class="controls">
				<label id="batch-serie-lbl" for="batch-serie-id" class="modalTooltip"
					   title="<?php echo JHtml::tooltipText('COM_SERMONSPEAKER_BATCH_SERIE_LABEL', 'COM_SERMONSPEAKER_BATCH_SERIE_LABEL_DESC'); ?>">
					<?php echo JText::_('COM_SERMONSPEAKER_BATCH_SERIE_LABEL'); ?>
				</label>
				<select name="batch[serie_id]" class="inputbox" id="batch-serie-id">
					<option value=""><?php echo JText::_('COM_SERMONSPEAKER_BATCH_SERIE_NOCHANGE'); ?></option>
					<?php echo JHtml::_('select.options', $this->series, 'value', 'text'); ?>
				</select>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="control-group span6">
			<div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.language', array()); ?>
			</div>
		</div>
		<div class="control-group span6">
			<div class="controls">
				<?php echo JLayoutHelper::render('joomla.html.batch.tag', array());?>
			</div>
		</div>
	</div>
	<?php if ($published >= 0) : ?>
		<div class="row-fluid">
			<div class="control-group span6">
				<div class="controls">
					<?php echo JLayoutHelper::render('joomla.html.batch.item', array('extension' => 'com_sermonspeaker')) ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
