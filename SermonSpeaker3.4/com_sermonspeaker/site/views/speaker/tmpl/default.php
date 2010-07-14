<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
// JHTML::_('behavior.tooltip');
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo $this->row->name.": ".JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE'); ?></th>
	</tr>
</table>
<!-- Begin Data - Speaker -->
<table border='0' cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<?php if ($this->row->pic) { ?>
			<td valign="middle" align="center" width='30%'>
				<img src="<?php echo $this->row->pic; ?>" border="0" alt="" />
			</td>
		<?php } ?>
		<td align="left" valign="top">
		<?php
		if ($this->row->website && $this->row->website != "http://") { ?>
			<a href="<?php echo $this->row->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->row->name); ?></a>
		<?php }
		if ($this->row->bio || $this->row->intro) { ?>
			<p><b><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?>:</b>
			<?php if($this->params->get('speaker_intro')) {
				echo $this->row->intro;
			}
			echo $this->row->bio; ?>
			</p>
		<?php } ?>
		</td>
	</tr>
</table>
<p></p>
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

