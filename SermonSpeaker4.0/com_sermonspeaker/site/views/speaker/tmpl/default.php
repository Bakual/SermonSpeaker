<?php
defined('_JEXEC') or die('Restricted access');
?>
<div class="ss-speaker-container" >
<h1 class="componentheading"><?php echo $this->row->name.": ".JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE'); ?></h1>
<!-- Begin Data - Speaker -->
<?php if($this->row->pic) { ?>
	<a href="<?php echo JRoute::_('index.php?view=speaker&id='.$this->row->id); ?>">
		<img class="speaker" src="<?php echo $this->row->pic; ?>" title="<?php echo $this->row->name; ?>" alt="<?php echo $this->row->name; ?>" />
	</a>
<?php }
if ($this->row->bio || ($this->row->intro && $this->params->get('speaker_intro'))) { ?>
	<h3 class="contentheading"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
	<?php
	echo $this->row->intro;
	echo $this->row->bio; ?>
	</p>
<?php }
if ($this->row->website && $this->row->website != "http://") { ?>
	<a href="<?php echo $this->row->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->row->name); ?></a>
<?php } ?>
<br style="clear:both" />
<!-- Begin Data - Series -->
<?php if($this->series) { ?>
	<table border="0" cellpadding="2" cellspacing="1" width="100%">
		<tr>
			<?php if ($this->av > 0){ ?>
				<th align="left" ></th>
			<?php } ?>
			<th align="left" ><?php echo JText::_('COM_SERMONSPEAKER_SERIESTITLE'); ?></th>		  
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SERIESDESCRIPTION'); ?></th>
		</tr>
		<?php
		$i = 0;
		foreach($this->series as $serie) {
			echo "<tr class=\"row$i\">\n"; 
			$i = 1 - $i;
			if ($this->av > 0){ ?>
				<td align="left" valign="top"  width="80">
					<?php if ($serie->avatar != '') { echo "<img src='".SermonspeakerHelperSermonspeaker::makelink($serie->avatar)."' >";} ?>
				</td>		  
			<?php } ?>
				<td align="left" valign="middle" width="125">
					<a title='<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>' href="<?php echo JRoute::_("index.php?view=serie&id=$serie->id"); ?>">
						<?php echo $serie->series_title; ?>
					</a>
				</td>
				<td align="left" valign="middle" >
					<?php echo $serie->series_description; ?>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERIES')); ?></div>
<?php } ?>
</div>