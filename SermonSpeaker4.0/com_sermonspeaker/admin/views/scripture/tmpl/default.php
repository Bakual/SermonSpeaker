<?php
defined('_JEXEC') or die;
?>
<h1><?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?></h1>
<form name="adminForm" id="adminForm">
	<table class="adminlist">
		<tr>
			<th rowspan="2" valign="bottom"><label for="book"><?php echo JText::_('COM_SERMONSPEAKER_BOOK'); ?></label></th>
			<th colspan="2"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE_START'); ?></th>
			<th colspan="2"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE_END'); ?></th>
		</tr>
		<tr>
			<th><label for="cap1"><?php echo JText::_('COM_SERMONSPEAKER_CHAPTER'); ?></label></th>
			<th><label for="vers1"><?php echo JText::_('COM_SERMONSPEAKER_VERS'); ?></label></th>
			<th><label for="cap2"><?php echo JText::_('COM_SERMONSPEAKER_CHAPTER'); ?></label></th>
			<th><label for="vers2"><?php echo JText::_('COM_SERMONSPEAKER_VERS'); ?></label></th>
		</tr>
		<tr>
			<td>
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
					<?php if ($n = $this->params->get('addbooks', 0)): ?>
						<optgroup label="<?php echo JText::_('COM_SERMONSPEAKER_CUSTOMBOOKS'); ?>">
						<?php for (; $i < (74 + $n); $i++): ?>
							<option value="<?php echo $i; ?>"><?php echo JText::_('COM_SERMONSPEAKER_BOOK_'.$i); ?></option>
						<?php endfor; ?>
						</optgroup>
					<?php endif; ?>
				</select>
			</td>
			<td><input name="from[cap1]" class="inputbox input-mini" id="cap1" type="text" size="3" /></td>
			<td><input name="from[vers1]" class="inputbox input-mini" id="vers1" type="text" size="3" /></td>
			<td><input name="from[cap2]" class="inputbox input-mini" id="cap2" type="text" size="3" /></td>
			<td><input name="from[vers2]" class="inputbox input-mini" id="vers2" type="text" size="3" /></td>
		</tr>
		<tr title="<?php echo JText::_('COM_SERMONSPEAKER_FREETEXT_DESC'); ?>">
			<td><label for="text"><?php echo JText::_('COM_SERMONSPEAKER_FREETEXT_LABEL'); ?></label></td>
			<td colspan="4"><input name="from[text]" class="inputbox input-large" id="text" type="text" size="60" /></td>
		</tr>
	</table>
	<div style="clear:left;"><br /></div>
	<button type="button" onclick="AddScripture();">
		<?php echo JText::_('JSAVE') ?>
	</button>
	<button type="button" onclick="window.parent.SqueezeBox.close();">
		<?php echo JText::_('JCANCEL') ?>
	</button>
</form>
