<?php
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
$session	= JFactory::getSession();
?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<h1><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME'); ?></h1>
	<table class="adminlist">
		<tr>
			<td><label for="diff">Apply difference</label></td>
			<td><input id="diff" name="diff" type="text" size="5" value="+1.0"></td>
			<td class="center"><a href="index.php?option=com_sermonspeaker&task=tools.time&mode=diff&<?php echo $session->getName().'='.$session->getId().'&'.JUtility::getToken(); ?>=1" target="_parent">
				<img src="<?php echo JURI::base()."components/com_sermonspeaker/images/save.png"; ?>" alt="<?php echo JText::_('JAPPLY'); ?>" title="<?php echo JText::_('JAPPLY'); ?>">
			</a></td>
		</tr>
		<tr>
			<td><label for="time">Set a fixed time</label></td>
			<td><input id="time" name="time" type="text" size="5" value="10:00"></td>
			<td class="center"><a href="index.php?option=com_sermonspeaker&task=tools.time&mode=time&<?php echo $session->getName().'='.$session->getId().'&'.JUtility::getToken(); ?>=1" target="_parent">
				<img src="<?php echo JURI::base()."components/com_sermonspeaker/images/save.png"; ?>" alt="<?php echo JText::_('JAPPLY'); ?>" title="<?php echo JText::_('JAPPLY'); ?>">
			</a></td>
		</tr>
	</table>
</form>