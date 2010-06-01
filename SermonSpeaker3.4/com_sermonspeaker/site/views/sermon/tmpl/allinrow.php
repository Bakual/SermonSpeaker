<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
// JHTML::_('behavior.tooltip');
$Itemid	= JRequest::getInt('Itemid');
if ($this->params->get('ga')) { $callback = "&callback=".$this->params->get('ga'); }
$return = SermonspeakerHelperSermonspeaker::insertAddfile($this->row[0]->addfile, $this->row[0]->addfileDesc);
$countcolumn = NULL; // will count optional columns so the popup button may span all columns
$id = $this->row[0]->id;
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo JText::_('SINGLESERMON'); ?></th>
	</tr>
</table>
<!-- Begin Header -->
<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<th align="left"><?php echo JText::_('SERMONNAME'); ?></th>
		<?php if ($this->params->get('client_col_sermon_scripture_reference')){ 
			$countcolumn++; ?>
			<th align="left"><?php echo JText::_('SCRIPTURE'); ?></th>
		<?php }
		if ($this->params->get('client_col_sermon_notes') && strlen($this->row[0]->notes) > 0){ 
			$countcolumn++; ?>
			<th align="left" valign="bottom"> <?php echo JText::_('SERMONNOTES'); ?></th>
		<?php }
		if ($return != NULL) {
			$countcolumn++; ?>
			<th align="left" valign="bottom"><?php echo JText::_('ADDFILE'); ?></th>
		<?php }
		if ($this->params->get('client_col_player') && strlen($this->row[0]->sermon_path) > 0){
			$countcolumn++; ?>
			<th align="left" valign="bottom"><?php echo JText::_('PLAY'); ?></th>
		<?php } ?>
	</tr>
<!-- Begin Data -->
	<tr>
		<td align='left' valign='top'>
			<?php if ($this->params->get('hide_dl') == "0" && strlen($this->row[0]->sermon_path) > 0) { ?>
				<a title='<?php echo JText::_('DOWNLOAD_HOOVER_TAG'); ?>' href='<?php echo $this->lnk; ?>'><?php echo $this->row[0]->sermon_title; ?></a>
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
			<td align="center" valign="top">
				<?php
				$ret = SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk);
				$pp_ret = explode("/",$ret);
				$pp_h = $pp_ret[0];
				$pp_w = $pp_ret[1];
				?>
			</td>
		<?php } ?>
	</tr>
	<?php $this->lnk = str_replace('\\','/',$this->lnk); ?>
	<tr>
		<?php
		if ($this->params->get('dl_button') == "1" && strlen($this->row[0]->sermon_path) > 0) {
			echo SermonspeakerHelperSermonspeaker::insertdlbutton($id, $this->row[0]->sermon_path);
		}
		if ($this->params->get('popup_player') == "1" && strlen($this->row[0]->sermon_path) > 0) {
			echo "<td colspan='".$countcolumn."'><input class=\"popup_btn\" type=\"button\" name=\"".JText::_('POPUP_PLAYER')."\" value=\"".JText::_('POPUP_PLAYER')."\" onClick=\"popup = window.open('".JRoute::_("index.php?view=sermon&layout=popup&id=$id&tmpl=component")."', 'PopupPage', 'height=".$pp_h.",width=".$pp_w.",scrollbars=yes,resizable=yes'); return false\"></td>";
		}
		?>
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
