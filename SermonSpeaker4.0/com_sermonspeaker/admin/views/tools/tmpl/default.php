<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
$session	= JFactory::getSession();
?>

<h1>Tools</h1>
<table class="adminform">
	<tbody><tr><td valign="top">
		<div id="cpanel">
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&task=tools.order&<?php echo $session->getName().'='.$session->getId().'&'.JUtility::getToken(); ?>=1">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ORDER'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/ordering.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ORDER'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&task=tools.write_id3&<?php echo $session->getName().'='.$session->getId().'&'.JUtility::getToken(); ?>=1">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ID3'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/download.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ID3'); ?></span>
				</a>
			</div>
		</div>
		<div style="clear: both;"> </div>
	</td></tr></tbody>
</table>	
