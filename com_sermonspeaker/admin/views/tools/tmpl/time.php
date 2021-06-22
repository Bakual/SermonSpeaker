<?php
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HtmlHelper::addIncludePath(JPATH_COMPONENT.'/helpers/html');
HtmlHelper::_('bootstrap.tooltip', '.hasTooltip');
$session	= JFactory::getSession();
?>
<form action="index.php?option=com_sermonspeaker&task=tools.time" target="_parent" method="post" id="adminForm" name="adminForm">
	<h4><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_TIME_REM'); ?></h4>
	<table class="adminlist">
		<tr>
			<td><label for="diff"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_TIME_DIFF'); ?></label></td>
			<td><input id="diff" name="diff" type="text" size="5" value="+1.5"></td>
			<td>
				<input type="submit" name="submit[diff]" value="<?php echo Text::_('JAPPLY'); ?>" />
			</td>
		</tr>
		<tr>
			<td><label for="time"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_TIME_SET'); ?></label></td>
			<td><input id="time" name="time" type="text" size="5" value="10:00"></td>
			<td>
				<input type="submit" name="submit[time]" value="<?php echo Text::_('JAPPLY'); ?>" />
			</td>
		</tr>
	</table>
	<div>
		<?php echo HtmlHelper::_('form.token'); ?>
	</div>
</form>