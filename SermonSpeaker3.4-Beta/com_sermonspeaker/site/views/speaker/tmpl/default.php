<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
// JHTML::_('behavior.tooltip');
$Itemid	= JRequest::getInt('Itemid');
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo $this->row->name.": ".JText::_('SINGLESPEAKER'); ?></th>
	</tr>
</table>
<!-- Begin Data - Speaker -->
<table cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<?php if ($this->row->pic) { ?>
			<td width="<?php echo $this->params->get('singlewidth'); ?>">
			<img src="<?php echo $this->row->pic; ?>" border="0" alt="" />
			</td>
		<?php } ?>
		<td align="left" valign="top">
		<p>
		<?php
		if ($this->row->website && $this->row->website != "http://") { ?>
			<br /><a href="<?php echo $this->row->website; ?>" target="_blank">
			<?php echo JText::_('WEB_LINK_TAG')." ".$this->row->name; ?></a>
		<?php }
		if ($this->row->bio) { ?>
			<br /> <?php echo JText::_('BIO'); ?>: <?php if($this->params->get('speaker_intro')) {
				echo $this->row->intro.'<br />';
			}
			echo $this->row->bio;
		} ?>
		</p>
		</td>
	</tr>
</table>
<p></p>
<!-- Begin Data - Series -->
<?php if( $this->series ) { ?>
	<table border="0" cellpadding="2" cellspacing="1" width="100%">
		<tr>
			<?php if ($this->av > 0){ ?>
				<th align="left" ></th>
			<?php } ?>
			<th align="left" ><?php echo JText::_('SERIESTITLE'); ?></th>		  
			<th align="left" valign="bottom"><?php echo JText::_('SERIESDESCRIPTION'); ?></th>
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
					<a TITLE="<?php echo JText::_('SERIES_SELECT_HOOVER_TAG'); ?> " href="<?php echo JRoute::_("index.php?view=serie&id=$serie->id"); ?>">
						<?php echo $serie->series_title; ?>
					</a>
				</td>
				<td align="left" valign="middle" >
					<?php echo $serie->series_description; ?>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>
