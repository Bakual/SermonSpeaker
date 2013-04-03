<?php
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
$session	= JFactory::getSession();
$user	= JFactory::getUser();
?>
<table class="adminform">
	<tbody><tr><td valign="top">
		<div id="cpanel">
			<div class="icon hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ID3').'::'.JText::_('COM_SERMONSPEAKER_TOOLS_ID3_DESC'); ?>">
				<a href="index.php?option=com_sermonspeaker&task=tools.write_id3&<?php echo $session->getName().'='.$session->getId().'&'.JSession::getFormToken(); ?>=1">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ID3'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/download.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ID3'); ?></span>
				</a>
			</div>
			<div class="icon hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME').'::'.JText::_('COM_SERMONSPEAKER_TOOLS_TIME_DESC'); ?>">
				<a class="modal" href="index.php?option=com_sermonspeaker&view=tools&layout=time&tmpl=component" rel="{handler: 'iframe', size: {x: 350, y: 170}}">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/clock.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME'); ?></span>
				</a>
			</div>
			<div class="icon hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_FIND').'::'.JText::_('COM_SERMONSPEAKER_TOOLS_FIND_DESC'); ?>">
				<a class="modal" href="index.php?option=com_sermonspeaker&view=files&layout=modal&tmpl=component" rel="{handler: 'iframe', size: {x: 700, y: 500}}">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_FIND'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/find.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_FIND'); ?></span>
				</a>
			</div>
			<?php // Check Access
				if (!$user->authorise('com_sermonspeaker.script', 'com_sermonspeaker')):
					$link = 'href="#" onclick="alert(\''.JText::_('JERROR_ALERTNOAUTHOR').'\')"';
					$class = ' inactive';
				else:
					$link = 'href="index.php?option=com_sermonspeaker&task=tools.createAutomatic"';
					$class = '';
				endif; ?>
			<div class="icon <?php echo $class; ?> hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_AUTOMATIC').'::'.JText::sprintf('COM_SERMONSPEAKER_TOOLS_AUTOMATIC_DESC', JURI::root()); ?>">
				<a <?php echo $link; ?>>
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_AUTOMATIC'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/run.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_AUTOMATIC'); ?></span>
				</a>
			</div>
			<?php if($this->pi): ?>
				<div class="icon hasTip" title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT').'::'.JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT_DESC'); ?>">
					<a href="index.php?option=com_sermonspeaker&task=tools.piimport&<?php echo $session->getName().'='.$session->getId().'&'.JSession::getFormToken(); ?>=1">
						<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/import.png"; ?>"/>
						<span><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT'); ?></span>
					</a>
				</div>
			<?php endif; ?>
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&view=statistics&format=raw">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_STATISTICS_TITLE'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/stats.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_STATISTICS_TITLE'); ?></span>
				</a>
			</div>
		</div>
		<div style="clear: both;"> </div>
	</td></tr></tbody>
</table>	
