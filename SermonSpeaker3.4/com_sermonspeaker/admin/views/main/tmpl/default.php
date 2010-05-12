<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

JToolBarHelper::title('SermonSpeaker', 'generic');
JToolbarHelper::spacer();
JToolbarHelper::divider();
JToolbarHelper::spacer();
JToolBarHelper::preferences('com_sermonspeaker',550);
?>

<table class="adminform">
	<?php echo $this->migrate; ?>
	<tbody><tr><td valign="top">
		<div id="cpanel">
			<div style="float: left;"><div class="icon">
				<a href="index.php?option=<?php echo $option; ?>&view=series">
					<img border="0" align="middle" alt="<?php echo JText::_('SERIESNAV'); ?>" src="<?php echo JURI::base()."components/".$option."/images/series.png"; ?>"/>
					<span><?php echo JText::_('SERIESNAV'); ?></span>
				</a>
			</div></div>
			<div style="float: left;"><div class="icon">
				<a href="index.php?option=<?php echo $option; ?>&view=speakers">
					<img border="0" align="middle" alt="<?php echo JText::_('SPEAKERSNAV'); ?>" src="<?php echo JURI::base()."components/".$option."/images/speakers.png"; ?>"/>
					<span><?php echo JText::_('SPEAKERSNAV'); ?></span>
				</a>
			</div></div>
			<div style="float: left;"><div class="icon">
				<a href="index.php?option=<?php echo $option; ?>&view=sermons">
					<img border="0" align="middle" alt="<?php echo JText::_('SERMONSNAV'); ?>" src="<?php echo JURI::base()."components/".$option."/images/sermon.png"; ?>"/>
					<span><?php echo JText::_('SERMONSNAV'); ?></span>
				</a>
			</div></div>
			<div style="float: left;"><div class="icon">
				<a href="index.php?option=<?php echo $option; ?>&view=statistics">
					<img border="0" align="middle" alt="<?php echo JText::_('STATISTICS'); ?>" src="<?php echo JURI::base()."components/".$option."/images/stats.png"; ?>"/>
					<span><?php echo JText::_('STATISTICS'); ?></span>
				</a>
			</div></div>
			<div style="float: left;"><div class="icon">
				<a href="index2.php?option=<?php echo $option; ?>&view=help">
					<img border="0" align="middle" alt="<?php echo JText::_('HELP'); ?>" src="<?php echo JURI::base()."components/".$option."/images/help.png"; ?>"/>
					<span><?php echo JText::_('HELP'); ?></span>
				</a>
			</div></div>
		</div>
		<div style="clear: both;"> </div>
	</td></tr></tbody>
</table>	
