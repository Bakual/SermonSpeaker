<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::stylesheet('com_sermonspeaker/sermonspeaker.css', '', true);
if (!$this->item->pic):
	$this->item->pic = 'media/com_sermonspeaker/images/nopict.jpg';
endif;
?>
<div class="ss-speaker-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>" >
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug)); ?>" target="_parent">
	<?php echo $this->item->name; ?>
</a></h2>
<div class="category-desc">
	<div class="ss-pic">
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug)); ?>" target="_parent">
			<img class="speaker img-polaroid" src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->item->pic); ?>" title="<?php echo $this->item->name; ?>" alt="<?php echo $this->item->name; ?>" />
		</a>
	</div>
	<?php if (($this->item->bio and in_array('speaker:bio', $this->columns)) or ($this->item->intro and in_array('speaker:intro', $this->columns))) : ?>
		<h3><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
		<?php 
		if (in_array('speaker:intro', $this->columns)):
			echo JHTML::_('content.prepare', $this->item->intro, '', 'com_sermonspeaker.intro');
		endif;
		if (in_array('speaker:bio', $this->columns)):
			echo JHTML::_('content.prepare', $this->item->bio, '', 'com_sermonspeaker.bio');
		endif;
	endif; ?>
	<div class="clear-left"></div>
	<?php if ($this->series): ?>
		<a class="badge" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERIESLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug).'&layout=series'); ?>" target="_parent">
			<?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?></a>&nbsp;
	<?php endif;
	if ($this->sermons): ?>
		<a class="badge" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERMONSLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug).'&layout=sermons'); ?>" target="_parent">
			<?php echo JText::_('COM_SERMONSPEAKER_SERMONS'); ?></a>&nbsp;
	<?php endif;
	if ($this->item->website and $this->item->website != 'http://') : ?>
		<a class="badge" href="<?php echo $this->item->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>">
			<?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->item->name); ?></a>
	<?php endif; ?>
</div>
</div>