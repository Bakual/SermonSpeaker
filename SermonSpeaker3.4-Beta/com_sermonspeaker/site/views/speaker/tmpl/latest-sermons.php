<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
// JHTML::_('behavior.tooltip');
$Itemid	= JRequest::getInt('Itemid');
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<?php if ($this->params->get('limit_speaker') == 1){ ?>
			<th align="left" valign="bottom"><?php echo $this->row->name.": ".$this->params->get('sermonresults').' '.JText::_('LATEST_SERMONS'); ?></th>
		<?php } else { ?>
			<th align="left" valign="bottom"><?php echo $this->row->name.": ".JText::_('SERMONS'); ?></th>
		<?php } ?>
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
<!-- Begin Data -->
<table cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<th align="left"><?php echo $title; ?></th>
		<th align="left"></th>
	</tr>
	<tr>
		<td align="left" valign="top" width="90%">
			<table border="0" cellpadding="2" cellspacing="1" width="100%">
				<tr>
					<?php if ($this->params->get('client_col_sermon_number')) { ?>
					<th width="5%" align="left" valign="bottom"><?php echo JText::_('SERMONNUMBER'); ?></th>
					<?php } ?>
					<th width="50%" align="left" valign="bottom"><?php echo JText::_('SERMONNAME'); ?></th>
					<?php if ($this->params->get('client_col_sermon_scripture_reference')) { ?>
					<th width="20%" align="left" valign="bottom"><?php echo JText::_('SCRIPTURE'); ?></th>
					<?php }
					if ($this->params->get('client_col_sermon_date')) { ?>
					<th width="10%" align="left" valign="bottom"><?php echo JText::_('SERMON_DATE'); ?></th>
					<?php }
					if ($this->params->get('client_col_sermon_time')) { ?>
					<th width="10%" align="center" valign="bottom"><?php echo JText::_('SERMONTIME'); ?></th>
					<?php } ?>
				</tr>
			<?php if( $this->sermons ) {
			$i = 0;
				foreach( $this->sermons as $sermon ) {
					echo "<tr class=\"row$i\">";
					$i = 1 - $i;
					if( $this->params->get('client_col_sermon_number')){
						echo "<td align=\"left\" valign=\"middle\" > $sermon->sermon_number </td>";
					}
					echo "<td><a title=\"".JText::_('SINGLE_SERMON_HOOVER_TAG')."\" href=\"".JRoute::_("index.php?view=sermon&id=$sermon->slug")."\">".$sermon->sermon_title."</a></td>";
					if( $this->params->get('client_col_sermon_scripture_reference')){
						echo "<td align=\"left\" valign=\"middle\" >$sermon->sermon_scripture</td>";
					}
					if( $this->params->get('client_col_sermon_date')){
						echo "<td align=\"left\" valign=\"middle\">".JHtml::_('date',$sermon->sermon_date,'%x',0)."</td>";
					}
					if( $this->params->get('client_col_sermon_time')){
						echo "<td align=\"center\" valign=\"middle\">".SermonspeakerHelperSermonspeaker::insertTime($sermon->sermon_time)."</td>";
					}
					echo "</tr>";
				} // end of foreach
			} ?>
			</table>
		</td>
	</tr>
</table>