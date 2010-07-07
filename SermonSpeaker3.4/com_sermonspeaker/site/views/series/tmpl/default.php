<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SERIES_TITLE').$this->cat; ?></th>
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
<form action="http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>" method="post" id="adminForm" name="adminForm">
<table cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<?php if($this->av) { echo "<th width='10'> </th>"; } ?>
		<th align="left"><?php echo JText::_('COM_SERMONSPEAKER_SERIESTITLE'); ?></th>
		<th align="left"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?></th>
		<th>player</th>
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
    		<td align="left" nowrap><a title='<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>' href="<?php echo JRoute::_("index.php?view=serie&id=$row->id" ); ?>"><?php echo $row->series_title; ?></a></td>
    		<td align="left">
				<?php echo $row->speakers; ?>
			</td>
			<td>
			<?php $player = JURI::root()."components/com_sermonspeaker/media/player/player.swf"; ?>
			<!-- Embed eingepackt in Object-Tag fuer Internet Explorer -->
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="250" height="20" id="player1" name="player1">
				<param name="movie" value="<?php echo $player; ?>"/>
				<param name="wmode" value="transparent"/>
				<param name="allowfullscreen" value="true"/>
				<param name="allowscriptaccess" value="always"/>
				<param name="flashvars" value="playlistfile=index.php%3Foption%3Dcom_sermonspeaker%26view%3Dfeed%26series_id%3D<?php echo $row->id; ?>"/>
				<embed src="<?php echo $player; ?>"
					width="250"
					height="20"
					wmode="transparent"
					allowscriptaccess="always"
					allowfullscreen="true"
					flashvars="playlistfile=index.php%3Foption%3Dcom_sermonspeaker%26view%3Dfeed%26series_id%3D<?php echo $this->row->id; ?>"
				/>
			</object>
			</td>
			</tr>
    	<?php } ?>
</table>
<br />
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getListFooter(); ?><br />
	</div>
</div>
</form>
