<?php
defined('_JEXEC') or die;
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$session	= JFactory::getSession();
?>
<h1>Tools</h1>
<table class="adminform">
	<tbody><tr><td valign="top">
		<div id="cpanel">
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&task=tools.write_id3&<?php echo $session->getName().'='.$session->getId().'&'.JSession::getFormToken(); ?>=1">
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
			<div class="icon">
				<a class="modal" href="index.php?option=com_sermonspeaker&view=tools&layout=time&tmpl=component" rel="{handler: 'iframe', size: {x: 350, y: 170}}">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/clock.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&task=tools.createAutomatic&<?php echo $session->getName().'='.$session->getId().'&'.JSession::getFormToken(); ?>=1">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_AUTOMATIC'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/run.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_AUTOMATIC'); ?></span>
				</a>
			</div>
			<?php if($this->pi): ?>
				<div class="icon">
					<a href="index.php?option=com_sermonspeaker&task=tools.piimport&<?php echo $session->getName().'='.$session->getId().'&'.JSession::getFormToken(); ?>=1">
						<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/import.png"; ?>"/>
						<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT'); ?></span>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<div style="clear: both;"> </div>
	</td></tr></tbody>
</table>	
