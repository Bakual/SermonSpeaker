<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo JText::_('SERIESMAIN'); ?></th>
	</tr>
</table>
<p />
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getResultsCounter(); ?><br />
		<?php if ($this->pagination->getPagesCounter()) echo $this->pagination->getPagesCounter()."<br />"; ?>
		<?php if ($this->pagination->getPagesLinks()) echo $this->pagination->getPagesLinks()."<br />"; ?>
	</div>
</div>

<hr style="width: 100%; height: 2px;" />
<table cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<?php if($this->av) { echo "<th width='10'> </th>"; } ?>
		<th align="left"><?php echo JText::_('SERMONNAME'); ?></th>
		<th align="left"><?php echo JText::_('SPEAKER'); ?></th>
	</tr>
	<?php
	$i = 0;
	$base = JURI::root();
    	foreach($this->rows as $row) {
			echo "<tr class=\"row$i\">\n"; 
			$i = 1 - $i;
			if ($this->av) {
				if ($row->avatar != '') { 
					echo "<td><img src='".SermonspeakerHelperSermonspeaker::makelink($row->avatar)."' ></td>";
				} else { 
					echo "<td> </td>"; 
				} 
			}
			?> 
    		<td align="left"><a href="<?php echo JRoute::_("index.php?view=serie&id=$row->id" ); ?>"><?php echo $row->series_title; ?></a></td>
    		<td align="left">
				<?php echo SermonspeakerHelperSermonspeaker::SpeakerTooltip($row->s_id, $row->pic, $row->name); ?>
			</td>
			</tr>
    	<?php } ?>
</table>
<br />
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getPagesLinks(); ?><br />
	</div>
</div>
