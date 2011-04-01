<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
if (!$this->speaker->pic):
	$this->speaker->pic = 'media/com_sermonspeaker/images/nopict.jpg';
endif;
?>
<div class="ss-speaker-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>" >
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->speaker->slug)); ?>" target="_parent">
	<?php echo $this->speaker->name; ?>
</a></h2>
<div class="category-desc">
	<div class="ss-pic">
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->speaker->slug)); ?>" target="_parent">
			<img class="speaker" src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->speaker->pic); ?>" title="<?php echo $this->speaker->name; ?>" alt="<?php echo $this->speaker->name; ?>" />
		</a>
	</div>
	<?php if ($this->speaker->bio || ($this->speaker->intro && $this->params->get('speaker_intro'))) : ?>
		<h3><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
		<?php 
		echo JHTML::_('content.prepare', $this->speaker->intro);
		echo JHTML::_('content.prepare', $this->speaker->bio);
	endif; ?>
	<div class="clear-left"></div>
	<?php if ($this->speaker->website && $this->speaker->website != 'http://') : ?>
		<a href="<?php echo $this->speaker->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->speaker->name); ?></a>
	<?php endif; ?>
</div>