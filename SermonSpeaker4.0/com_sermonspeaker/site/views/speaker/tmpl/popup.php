<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
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
			<img class="speaker" src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->item->pic); ?>" title="<?php echo $this->item->name; ?>" alt="<?php echo $this->item->name; ?>" />
		</a>
	</div>
	<?php if (($this->item->bio && in_array('speaker:bio', $this->columns)) || ($this->item->intro && in_array('speaker:intro', $this->columns))) : ?>
		<h3><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
		<?php 
		if (in_array('speaker:intro', $this->columns)):
			echo JHTML::_('content.prepare', $this->item->intro);
		endif;
		if (in_array('speaker:bio', $this->columns)):
			echo JHTML::_('content.prepare', $this->item->bio);
		endif;
	endif; ?>
	<div class="clear-left"></div>
	<?php if ($this->item->website && $this->item->website != 'http://') : ?>
		<a href="<?php echo $this->item->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->item->name); ?></a>
	<?php endif; ?>
</div>
</div>