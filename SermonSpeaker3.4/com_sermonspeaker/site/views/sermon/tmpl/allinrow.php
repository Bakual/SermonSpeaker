<?php
defined('_JEXEC') or die('Restricted access');
if ($this->params->get('ga')) { $callback = "&callback=".$this->params->get('ga'); }
?>
<div id="ss-sermon-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_TITLE'); ?></h1>
<!-- Begin Header -->
<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<th align="left"><?php echo JText::_('COM_SERMONSPEAKER_SERMONTITLE'); ?></th>
		<?php if ($this->params->get('client_col_sermon_scripture_reference')){ ?>
			<th align="left"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?></th>
		<?php }
		if ($this->params->get('client_col_sermon_notes') && strlen($this->row->notes) > 0){ ?>
			<th align="left" valign="bottom"> <?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?></th>
		<?php }
		if ($this->params->get('client_col_sermon_addfile') && $this->row->addfile && $this->row->addfileDesc) { ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></th>
		<?php }
		if ($this->params->get('client_col_player') && strlen($this->row->sermon_path) > 0){ ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_PLAYER'); ?></th>
		<?php } ?>
	</tr> 
<!-- Begin Data -->
	<tr>
		<td align='left' valign='top'>
			<?php if ($this->params->get('hide_dl') == "0" && strlen($this->row->sermon_path) > 0) { ?>
				<a title='<?php echo JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER'); ?>' href='<?php echo $this->lnk; ?>'><?php echo $this->row->sermon_title; ?></a>
			<?php } else {
				echo $this->row->sermon_title;
			} ?>
		</td>
		<?php if ($this->params->get('client_col_sermon_scripture_reference')){ ?>
			<td align="left" valign="top"><?php echo $this->row->sermon_scripture; ?></td>
		<?php }
		if ($this->params->get('client_col_sermon_notes') && strlen($this->row->notes) > 0){ ?>
			<td align="left" valign="top"><?php echo $this->row->notes; ?></td>
		<?php }
		if ($this->params->get('client_col_sermon_addfile') && $this->row->addfile && $this->row->addfileDesc) { ?>
			<td align="left" valign="top"><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->row->addfile, $this->row->addfileDesc); ?></td>
		<?php }
		if ($this->params->get('client_col_player') && strlen($this->row->sermon_path) > 0){ ?> 
			<td align="center" valign="top">
				<?php
				$ret = SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk, $this->row->sermon_time);
				$pp_ret = explode("/",$ret);
				$pp_h = $pp_ret[0];
				$pp_w = $pp_ret[1];
				?>
			</td>
		<?php } ?>
	</tr>
	<?php $this->lnk = str_replace('\\','/',$this->lnk); ?>
</table>
<div style="float:left;">
	<?php if ($this->params->get('dl_button') == "1" && strlen($this->row->sermon_path) > 0) {
		echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->row->id, $this->row->sermon_path);
	} ?>
</div>
<div style="float:right;">
	<?php if ($this->params->get('popup_player') == "1" && strlen($this->row->sermon_path) > 0) { ?>
	<input class="popup_btn button" type="button" name="<?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>" value="<?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>" onClick="popup=window.open('<?php echo JRoute::_('index.php?view=sermon&layout=popup&id='.$this->row->id.'&tmpl=component'); ?>', 'PopupPage','height=<?php echo $pp_h; ?>,width=<?php echo $pp_w; ?>,scrollbars=yes,resizable=yes'); return false;">
	<?php } ?>
</div>
<table width="100%" style="clear:both;">
	<?php
	// Support for JComments
	$comments = JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php';
	if (file_exists($comments)) {
		require_once($comments); ?>
		<tr><td><br></td></tr>
		<tr>
			<td>
				<?php echo JComments::showComments($this->row->id, 'com_sermonspeaker', $this->row->sermon_title); ?>
			</td>
		</tr>
	<?php } ?>
</table>
</div>