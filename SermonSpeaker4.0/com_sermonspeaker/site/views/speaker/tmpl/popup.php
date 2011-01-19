<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
if ($this->speaker->pic == "") { $this->speaker->pic = JURI::root().'components/com_sermonspeaker/images/nopict.jpg'; }
?>
<!-- Begin Data -->
<div id="ss-speaker-container" style="padding:20px;">
<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->speaker->slug)); ?>" target="_parent">
	<img class="speaker" style="float:right;" src="<?php echo $this->speaker->pic; ?>" title="<?php echo $this->speaker->name; ?>" alt="<?php echo $this->speaker->name; ?>" />
</a>
<h1 class="componentheading"><?php echo $this->speaker->name ?></h1>
<?php if ($this->speaker->bio || ($this->speaker->intro && $this->params->get('speaker_intro'))) { ?>
	<h3 class="contentheading"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
	<?php
	echo $this->speaker->intro;
	echo $this->speaker->bio; ?>
<?php }
if ($this->speaker->website && $this->speaker->website != 'http://') { ?>
	<a href="<?php echo $this->speaker->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->speaker->name); ?></a>
<?php } ?>
</div>