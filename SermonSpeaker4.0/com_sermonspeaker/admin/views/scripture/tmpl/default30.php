<?php
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'serie.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="#" method="post" name="adminForm" id="adminForm" class="form-validate form-inline">
	<legend><?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?></legend>
	<div class="row-fluid center">
		<div class="span4 offset4">
			<?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE_START'); ?>
		</div>
		<div class="span3 offset1">
			<?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE_END'); ?>
		</div>
	</div>
	<div class="row-fluid center">
		<div class="span4">
			<label for="book"><?php echo JText::_('COM_SERMONSPEAKER_BOOK'); ?></label>
		</div>
		<div class="span3">
			<label for="cap1"><?php echo JText::_('COM_SERMONSPEAKER_CHAPTER'); ?></label>
			<?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR'); ?>
			<label for="vers1"><?php echo JText::_('COM_SERMONSPEAKER_VERS'); ?></label>
		</div>
		<div class="span1">-</div>
		<div class="span3">
			<label for="cap2"><?php echo JText::_('COM_SERMONSPEAKER_CHAPTER'); ?></label>
			<?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR'); ?>
			<label for="vers2"><?php echo JText::_('COM_SERMONSPEAKER_VERS'); ?></label>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span4">
			<select name="from[book]" class="inputbox input-medium" id="book">
				<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_BOOK'); ?></option>
				<optgroup label="<?php echo JText::_('COM_SERMONSPEAKER_OLD_TESTAMENT'); ?>">
				<?php for ($i = 1; $i < 40; $i++): ?>
					<option value="<?php echo $i; ?>"><?php echo JText::_('COM_SERMONSPEAKER_BOOK_'.$i); ?></option>
				<?php endfor; ?>
				</optgroup>
				<optgroup label="<?php echo JText::_('COM_SERMONSPEAKER_NEW_TESTAMENT'); ?>">
				<?php for (; $i < 67; $i++): ?>
					<option value="<?php echo $i; ?>"><?php echo JText::_('COM_SERMONSPEAKER_BOOK_'.$i); ?></option>
				<?php endfor; ?>
				</optgroup>
				<optgroup label="<?php echo JText::_('COM_SERMONSPEAKER_APOCRYPHA'); ?>">
				<?php for (; $i < 74; $i++): ?>
					<option value="<?php echo $i; ?>"><?php echo JText::_('COM_SERMONSPEAKER_BOOK_'.$i); ?></option>
				<?php endfor; ?>
				</optgroup>
			</select>
		</div>
		<div class="span3">
			<input name="from[cap1]" class="inputbox span5" id="cap1" type="text" size="3" />
			<?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR'); ?>
			<input name="from[vers1]" class="inputbox span5" id="vers1" type="text" size="3" />
		</div>
		<div class="span1">-</div>
		<div class="span3">
			<input name="from[cap2]" class="inputbox span5" id="cap2" type="text" size="3" />
			<?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR'); ?>
			<input name="from[vers2]" class="inputbox span5" id="vers2" type="text" size="3" />
		</div>
	</div>
	<div class="row-fluid" rel="tooltip" title="<?php echo JText::_('COM_SERMONSPEAKER_FREETEXT_DESC'); ?>">
		<div class="span4">
			<label for="text"><?php echo JText::_('COM_SERMONSPEAKER_FREETEXT_LABEL'); ?></label>
		</div>
		<div class="span7">
			<input name="from[text]" class="inputbox input-large" id="text" type="text" size="60" />
		</div>
	</div>
	<button type="button" class="btn btn-primary" onclick="AddScripture();">
		<?php echo JText::_('JSAVE') ?>
	</button>
	<button type="button" class="btn" onclick="window.parent.SqueezeBox.close();">
		<?php echo JText::_('JCANCEL') ?>
	</button>
</form>
