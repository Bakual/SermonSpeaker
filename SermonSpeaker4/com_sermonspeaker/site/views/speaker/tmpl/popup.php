<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!-- Begin Data -->
<?php if ($this->row->pic == "") { $this->row->pic = JURI::root().'components/com_sermonspeaker/images/nopict.jpg'; } ?>
<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
	<tbody>
		<tr>
			<td style="width: 50px;"> </td>
			<td style="width: 1025px;"><h3><?php echo $this->row->name;?></h3></td>
			<td rowspan='2' style="width: 300px;"><img src="<?php echo $this->row->pic;?>" alt="<?php echo $this->row->name;?>"></td>
		</tr>
		<tr>
			<td> </td>
			<td>
				<?php
				if ($this->row->website) {
					echo '<br /><a href="'.$this->row->website.'" target="_blank" title="'.JText::_('WEB_LINK_DESCRIPTION').'">'.JText::_('WEB_LINK_TAG').' '.$this->row->name.'</a><br />';
				}
				if ($this->row->intro || $this->row->bio){
					echo '<br /><b>'.JText::_('BIO').':</b>';
					echo $this->row->intro;
					echo $this->row->bio;
				}
				?>
			</td>
		</tr>
	</tbody>
</table>
