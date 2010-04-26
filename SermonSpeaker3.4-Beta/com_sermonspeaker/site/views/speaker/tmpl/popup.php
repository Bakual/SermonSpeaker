<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!-- Begin Data -->
<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
	<tbody>
		<tr>&nbsp;</tr>
		<tr>
			<td style="width: 50px;"> </td>
			<td style="width: 1025px;"><h3><?php echo $this->row->name;?></h3></td>
			<td style="width: 300px;"><img alt="<?php echo $this->row->name;?>" src="<?php echo $this->row->pic;?>"></td>
		</tr>
		<tr>
			<td style="width: 50px;"> </td>
			<td colspan="2" rowspan="1" style="width: 1000px;">
				<?php
				if ($this->row->website) {
					echo "<br /><A HREF=\"".$this->row->website."\" target=\"blank\" title=\"".JText::_('WEB_LINK_DESCRIPTION')."\">".JText::_('WEB_LINK_TAG')." ".$this->row->name."</A><br />";
				}
				if ($this->params->get('speaker_intro') && $this->row->intro) {
					echo "<br />".$this->row->intro."<br />";
				}
				if ($this->row->bio) { 
					echo "<br /><b>".JText::_('BIO').": </b><br />".$this->row->bio."<br />";
				} 
				?>
			</td>
			<td style="width: 50px;"> </td>
		</tr>
	</tbody>
</table>
