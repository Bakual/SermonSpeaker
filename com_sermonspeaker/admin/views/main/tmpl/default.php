<?php
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
?>
<table class="adminform">
	<tbody><tr><td valign="top">
		<div id="cpanel">
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&view=series">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_SERIES_TITLE'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/series.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_SERIES_TITLE'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&view=speakers">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/speakers.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&view=sermons">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/sermon.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&view=tags">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_TAGS_TITLE'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/tags.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_TAGS_TITLE'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_categories&extension=com_sermonspeaker">
					<img border="0" align="middle" alt="<?php echo JText::_('JCATEGORIES'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/category.png"; ?>"/>
					<span><?php echo JText::_('JCATEGORIES'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&view=tools">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_MAIN_TOOLS'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/tools.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_MAIN_TOOLS'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&view=languages">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_MAIN_LANGUAGES'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/globe.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_MAIN_LANGUAGES'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_sermonspeaker&view=help">
					<img border="0" align="middle" alt="<?php echo JText::_('COM_SERMONSPEAKER_MENU_HELP'); ?>" src="<?php echo JURI::base()."components/com_sermonspeaker/images/help.png"; ?>"/>
					<span><?php echo JText::_('COM_SERMONSPEAKER_MENU_HELP'); ?></span>
				</a>
			</div>
		</div>
		<div style="clear: both;"> </div>
	</td></tr></tbody>
</table>	
