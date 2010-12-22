<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>
<div id="ss-sermon-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_TITLE'); ?></h1>
<!-- Begin Data -->
<?php if ($this->params->get('hide_dl') == "0" && strlen($this->row->sermon_path) > 0) {
	echo '<h3 class="contentheading"><a title="'.JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER').'" href="'.$this->lnk."\">".$this->row->sermon_title.'</a></h3>';
} else {
	echo '<h3 class="contentheading">'.$this->row->sermon_title.'</h3>';
} ?>
<div class="ss-sermondetail-container">
	<?php if ($this->params->get('client_col_sermon_scripture_reference') && $this->row->sermon_scripture){ ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo $this->row->sermon_scripture; ?></div>
	<?php }
	if ($this->params->get('client_col_player') && strlen($this->row->sermon_path) > 0){ ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text ss-sermon-player">
			<?php
			$ret = SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk, $this->row->sermon_time, 1, $this->row->sermon_title, $this->speaker->name);
			$pp_ret = explode("/",$ret);
			$pp_h = $pp_ret[0];
			$pp_w = $pp_ret[1];
			?>
		</div>
	<?php } // if client_col_player
	$this->lnk = str_replace('\\','/',$this->lnk);
	if ($this->params->get('dl_button') == "1" && strlen($this->row->sermon_path) > 0) { ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->row->id, $this->row->sermon_path); ?></div>
	<?php }
	if ($this->params->get('popup_player') == "1" && strlen($this->row->sermon_path) > 0) { ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->row->id, $ret); ?></div>
	<?php }
	if ($this->params->get('client_col_sermon_addfile') && $this->row->addfile) { ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:</div>
		<div class="ss-sermondetail-text">
			<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->row->addfile, $this->row->addfileDesc); ?>
		</div>
	<?php }
	if ($this->params->get('client_col_sermon_notes') && strlen($this->row->notes) > 0){ ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo $this->row->notes; ?></div>
	<?php } ?>
</div>
<table width="100%">
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