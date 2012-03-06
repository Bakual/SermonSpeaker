<?php
defined('_JEXEC') or die('Restricted access');

// JHTML::_('behavior.tooltip');
// JHTML::_('behavior.modal');
?>

<h1><?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?></h1>
<form name="adminForm" id="adminForm">
	<table class="adminlist">
		<tr>
			<th rowspan="2" valign="bottom"><label for="book">Buch</label></th>
			<th colspan="2">Von</th>
			<th colspan="2">Bis</th>
		</tr>
		<tr>
			<th><label for="cap1">Kapitel</label></th>
			<th><label for="vers1">Vers</label></th>
			<th><label for="cap2">Kapitel</label></th>
			<th><label for="vers2">Vers</label></th>
		</tr>
		<tr>
			<td>
				<select name="from[book]" class="inputbox" id="book">
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
			</td>
			<td><input name="from[cap]" class="inputbox" id="cap1" type="text" size="3" /></td>
			<td><input name="from[vers]" class="inputbox" id="vers1" type="text" size="3" /></td>
			<td><input name="from[cap]" class="inputbox" id="cap2" type="text" size="3" /></td>
			<td><input name="from[vers]" class="inputbox" id="vers2" type="text" size="3" /></td>
		</tr>
	</table>
	<div style="clear:left;"><br /></div>
	<button type="button" onclick="AddScripture();">
		<?php echo JText::_('JTOOLBAR_APPLY') ?>
	</button>
	<button type="button" onclick="window.parent.SqueezeBox.close();">
		<?php echo JText::_('JCANCEL') ?>
	</button>
</form>
