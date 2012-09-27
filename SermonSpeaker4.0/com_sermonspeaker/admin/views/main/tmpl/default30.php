<?php
defined('_JEXEC') or die;
?>
<div id="j-main-container">
	<ul class="thumbnails">
		<li class="span2">
			<a class="thumbnail" href="index.php?option=com_sermonspeaker&view=series">
				<img src="<?php echo JURI::base()."components/com_sermonspeaker/images/series.png"; ?>"/>
				<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_SERIES_TITLE'); ?></h3>
			</a>
		</li>
		<li class="span2">
			<a class="thumbnail" href="index.php?option=com_sermonspeaker&view=speakers">
				<img src="<?php echo JURI::base()."components/com_sermonspeaker/images/speakers.png"; ?>"/>
				<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'); ?></h3>
			</a>
		</li>
		<li class="span2">
			<a class="thumbnail" href="index.php?option=com_sermonspeaker&view=sermons">
				<img src="<?php echo JURI::base()."components/com_sermonspeaker/images/sermon.png"; ?>"/>
				<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'); ?></h3>
			</a>
		</li>
		<li class="span2">
			<a class="thumbnail" href="index.php?option=com_sermonspeaker&view=tags">
				<img src="<?php echo JURI::base()."components/com_sermonspeaker/images/tags.png"; ?>"/>
				<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_TAGS_TITLE'); ?></h3>
			</a>
		</li>
		<li class="span2">
			<a class="thumbnail" href="index.php?option=com_categories&extension=com_sermonspeaker">
				<img src="<?php echo JURI::base()."components/com_sermonspeaker/images/category.png"; ?>"/>
				<h3 class="center"><?php echo JText::_('JCATEGORIES'); ?></h3>
			</a>
		</li>
		<li class="span2">
			<a class="thumbnail" href="index.php?option=com_sermonspeaker&view=tools">
				<img src="<?php echo JURI::base()."components/com_sermonspeaker/images/tools.png"; ?>"/>
				<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_MAIN_TOOLS'); ?></h3>
			</a>
		</li>
	</ul>
	<ul class="thumbnails">
		<li class="span2">
			<a class="thumbnail" href="index.php?option=com_sermonspeaker&view=help">
				<img src="<?php echo JURI::base()."components/com_sermonspeaker/images/help.png"; ?>"/>
				<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_MENU_HELP'); ?></h3>
			</a>
		</li>
	</ul>
</div>