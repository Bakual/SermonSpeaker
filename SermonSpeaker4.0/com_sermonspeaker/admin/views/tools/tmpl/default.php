<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
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
			<div class="icon">
				<a class="modal" href="index.php?option=com_sermonspeaker&view=files&layout=modal&tmpl=component" rel="{handler: 'iframe', size: {x: 700, y: 500}}">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_FIND'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/find.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_FIND'); ?></span>
				</a>
			</div>
			<?php if($this->pi): ?>
				<div class="icon">
					<a href="index.php?option=com_sermonspeaker&task=tools.piimport&<?php echo $session->getName().'='.$session->getId().'&'.JUtility::getToken(); ?>=1">
						<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/import.png"; ?>"/>
						<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT'); ?></span>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<div style="clear: both;"> </div>
	</td></tr></tbody>
</table>	
