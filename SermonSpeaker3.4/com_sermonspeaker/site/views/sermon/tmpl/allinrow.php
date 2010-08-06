<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>
<div id="ss-sermon-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_TITLE'); ?></h1>
<!-- Begin Header -->
<h3 class="contentheading">
<?php if ($this->params->get('hide_dl') == "0" && strlen($this->row->sermon_path) > 0) { ?>
	<a title='<?php echo JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER'); ?>' href='<?php echo $this->lnk; ?>'><?php echo $this->row->sermon_title; ?></a>
<?php } else {
	echo $this->row->sermon_title;
} ?>
</h3>
<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<?php if ($this->params->get('client_col_sermon_scripture_reference')){ ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?></th>
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
				$ret = SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk, $this->row->sermon_time, 1, $this->row->sermon_title, $this->speaker->name);
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
	<?php if ($this->params->get('popup_player') == "1" && strlen($this->row->sermon_path) > 0) {
		echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->row->id, $ret);
	} ?>
</div>
<table width="100%" style="clear:both;">
	<?php
	// Support for JComments
	$comments = JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php';
	if (file_exists($comments)) {
		require_once($comments); ?>
		<tr><td><br /></td></tr>
		<tr>
			<td>
				<?php echo JComments::showComments($this->row->id, 'com_sermonspeaker', $this->row->sermon_title); ?>
			</td>
		</tr>
	<?php } ?>
</table>
</div>