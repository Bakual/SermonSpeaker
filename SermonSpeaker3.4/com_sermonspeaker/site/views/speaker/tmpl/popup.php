<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
if ($this->row->pic == "") { $this->row->pic = JURI::root().'components/com_sermonspeaker/images/nopict.jpg'; }
?>
<!-- Begin Data -->
<div class="ss-speaker-container" style="padding:20px;">
<a href="<?php echo JRoute::_('index.php?view=speaker&id='.$this->row->id); ?>" target="_parent>
	<img class="speaker" style="float:right;" src="<?php echo $this->row->pic; ?>" title="<?php echo $this->row->name; ?>" alt="<?php echo $this->row->name; ?>" />
</a>
<h1 class="componentheading"><?php echo $this->row->name ?></h1>
<?php if ($this->row->bio || ($this->row->intro && $this->params->get('speaker_intro'))) { ?>
	<h3 class="contentheading"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
	<?php
	echo $this->row->intro;
	echo $this->row->bio; ?>
<?php }
if ($this->row->website && $this->row->website != "http://") { ?>
	<a href="<?php echo $this->row->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->row->name); ?></a>
<?php } ?>
</div>