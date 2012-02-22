<?php
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
$session	= JFactory::getSession();
?>
<form action="index.php?option=com_sermonspeaker&task=tools.time" target="_parent" method="post" id="adminForm" name="adminForm">
	<h1><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME'); ?></h1>
	<table class="adminlist">
		<tr>
			<td><label for="diff">Apply difference</label></td>
			<td><input id="diff" name="diff" type="text" size="5" value="+1.5"></td>
			<td>
				<input type="submit" name="submit[diff]" value="<?php echo JText::_('JAPPLY'); ?>" />
			</td>
		</tr>
		<tr>
			<td><label for="time">Set to a fixed time</label></td>
			<td><input id="time" name="time" type="text" size="5" value="10:00"></td>
			<td>
				<input type="submit" name="submit[time]" value="<?php echo JText::_('JAPPLY'); ?>" />
			</td>
		</tr>
	</table>
	<div>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>