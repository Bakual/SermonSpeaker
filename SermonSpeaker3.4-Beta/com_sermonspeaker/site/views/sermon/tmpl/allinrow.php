<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
// JHTML::_('behavior.tooltip');
$Itemid	= JRequest::getInt('Itemid');
global $option;
if ($this->params->get('ga')) { $callback = "&callback=".$this->params->get('ga'); }
$return = SermonspeakerHelperSermonspeaker::insertAddfile($this->row[0]->addfile, $this->row[0]->addfileDesc);
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo JText::_('SINGLESERMON'); ?></th>
	</tr>
</table>
<!-- Begin Data -->
<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<th align="left"><?php echo JText::_('SERMONNAME'); ?></th>
		<?php if ($this->params->get('client_col_sermon_scripture_reference')){ ?>
			<th align="left"><?php echo JText::_('SCRIPTURE'); ?></th>
		<?php }
		if ($this->params->get('client_col_sermon_notes') && strlen($this->row[0]->notes) > 0){ ?>
			<th align="left" valign="bottom"> <?php echo JText::_('SERMONNOTES'); ?></th>
		<?php }
		if ($return != NULL) { ?>
			<th align="left" valign="bottom"><?php echo JText::_('ADDFILE'); ?></th>
		<?php }
		if ($this->params->get('client_col_player') && strlen($this->row[0]->sermon_path) > 0){ ?>
			<th align="left" valign="bottom"><?php echo JText::_('PLAY'); ?></th>
		<?php } ?>
	</tr>
	<tr>
		<td align="left" valign="top">
			<?php if ($this->params->get('hide_dl') == "0" && strlen($this->row[0]->sermon_path) > 0) { ?>
				<a TITLE="<?php echo JText::_('DOWNLOAD_HOOVER_TAG').'" href="'.$this->lnk.'">'.$this->row[0]->sermon_title; ?></a>
			<?php } else {
				echo $this->row[0]->sermon_title;
			} ?>
		</td>
		<?php if ($this->params->get('client_col_sermon_scripture_reference')){ ?>
			<td align="left" valign="top"><?php echo $this->row[0]->sermon_scripture; ?></td>
		<?php }
		if ($this->params->get('client_col_sermon_notes') && strlen($this->row[0]->notes) > 0){ ?>
			<td align="left" valign="top"><?php echo $this->row[0]->notes; ?></td>
		<?php }
		if ($return != NULL) { ?>
			<td align="left" valign="top"><?php echo $return; ?></td>
		<?php }
		if ($this->params->get('client_col_player') && strlen($this->row[0]->sermon_path) > 0){ ?> 
			<td valign="top"><br /><center>
				<?php
				$ret = SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk);
				$pp_ret = explode("/",$ret);
				$pp_h = $pp_ret[0];
				$pp_w = $pp_ret[1];
				?>
			</center><br /></td>
		<?php } ?>
	</tr>
	<?php $this->lnk = str_replace('\\','/',$this->lnk);
	$id = $this->row[0]->id; ?>
	<tr>
		<td></td>
		<?php if ($this->params->get('dl_button') == "1" && strlen($this->row[0]->sermon_path) > 0) {
			echo SermonspeakerHelperSermonspeaker::insertdlbutton($option,$id,$Itemid,$this->row[0]->sermon_path);
		}
		if ($this->params->get('popup_player') == "1" && strlen($this->row[0]->sermon_path) > 0) {
			echo "<td><input style=\"font-size:12px;color:#000066;font-family:verdana;\" type=\"button\" name=\"".JText::_('POPUP_PLAYER')."\" value=\"".JText::_('POPUP_PLAYER')."\" onClick=\"popup = window.open('".JRoute::_("index2.php?option=$option&view=sermon&layout=popup&id=$id&Itemid=$Itemid")."', 'PopupPage', 'height=".$pp_h.",width=".$pp_w.",scrollbars=yes,resizable=yes'); return false\"></td>";
		} ?>
	</tr>
</table>
<table width="100%">
	<?php
	// Support for JComments
	$comments = JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php';
	if (file_exists($comments)) {
		require_once($comments); ?>
		<tr><td><br></td></tr>
		<tr>
			<td>
				<?php echo JComments::showComments($id, 'com_sermonspeaker', $this->row[0]->sermon_title); ?>
			</td>
		</tr>
	<?php } ?>
</table>
