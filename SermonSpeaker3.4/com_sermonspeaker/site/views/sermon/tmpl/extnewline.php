<?php
defined('_JEXEC') or die('Restricted access');
if ($this->params->get('ga')) { $callback = "&callback=".$this->params->get('ga'); }
$return = SermonspeakerHelperSermonspeaker::insertAddfile($this->row->addfile, $this->row->addfileDesc);
$id = $this->row->id;
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>
<div id="ss-sermon-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_TITLE'); ?></h1>
<!-- Begin Data -->
<?php if ($this->params->get('hide_dl') == "0" && strlen($this->row->sermon_path) > 0) { ?>
	<h3 class="contentheading"><a title="<?php echo JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER'); ?>" href="<?php echo $this->lnk.'">'.$this->escape($this->row->sermon_title); ?></a></h3>
<?php } else { ?>
	<h3 class="contentheading"><?php echo $this->escape($this->row->sermon_title); ?></h3>
<?php } ?>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td valign="top"><b><?php echo JText::_('COM_SERMONSPEAKER_SERMONDATE'); ?>:</b></td>
		<td><?php echo JHTML::date($this->row->sermon_date, JText::_($this->params->get('date_format')), 0); ?></td>
	</tr>
	<tr>
		<td valign="top"><b><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?>:</b></td>
		<td><?php echo $this->row->sermon_scripture; ?></td>
	</tr>
	<?php if ($this->params->get('client_col_custom1') == "1" && strlen($this->row->custom1) > 0) { ?>
		<tr>
			<td valign="top"><b><?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:</b></td>
			<td><?php echo $this->row->custom1; ?></td>
		</tr>
	<?php }
	if ($this->params->get('client_col_custom2') == "1" && strlen($this->row->custom2) > 0) { ?>
		<tr>
			<td valign="top"><b><?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:</b></td>
			<td><?php echo $this->row->custom2; ?></td>
		</tr>
	<?php } ?>
	<tr>
		<td valign="top"><b><?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:</b></td>
		<td>
			<a href="<?php echo JRoute::_('index.php?view=serie&id='.$this->serie->id); ?>">
			<?php echo $this->escape($this->serie->series_title); ?>
			</a>
		</td>
	</tr>
	<tr>
		<td valign="top"><b><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>:</b></td>
		<td>
			<?php echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($this->speaker->id, $this->speaker->pic, $this->speaker->name); ?>
		</td>
	</tr>
	<?php if ($this->speaker->pic) { ?>
	<tr>
		<td></td>
		<td><img height=150 src="<?php echo $this->speaker->pic; ?>"></td>
	</tr>
	<?php } ?>
	<tr>
		<td valign="top"><b><?php echo JText::_('COM_SERMONSPEAKER_SERMONTIME'); ?>:</b></td>
		<td><?php echo SermonspeakerHelperSermonspeaker::insertTime($this->row->sermon_time); ?></td>
	</tr>
	<tr>
		<td valign="top"><b><?php echo JText::_('COM_SERMONSPEAKER_HITS'); ?>:</b></td>
		<td><?php echo $this->row->hits; ?></td>
	</tr>
	<?php if ($this->params->get('client_col_sermon_notes') && strlen($this->row->notes) > 0){ ?>
		<tr>
			<td valign="top"><b><?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?>:</b></td>
			<td><?php echo $this->row->notes; ?></td>
		</tr>
	<?php }
	if ($this->params->get('client_col_player')){ ?>
	<tr>
		<td></td>
		<td>
			<br />
			<?php 
			$ret = SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk, $this->row->sermon_time, 1, $this->row->sermon_title, $this->speaker->name);
			$pp_ret = explode('/', $ret);
			$pp_h = $pp_ret[0];
			$pp_w = $pp_ret[1];
			?>
			<br />
		</td>
	</tr>
	<?php } // if client_col_player
	if ($this->params->get('popup_player') == "1") { 
	?>
		<tr><td></td>
		<td><input class="popup_btn button" type="button" name="<?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>" value="<?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>" onclick="popup = window.open('<?php echo JRoute::_("index.php?view=sermon&layout=popup&id=$id&tmpl=component"); ?>', 'PopupPage', 'height=<?php echo $pp_h.",width=".$pp_w; ?>,scrollbars=yes,resizable=yes'); return false" /></td></tr>
	<?php }
	if ($this->params->get('dl_button') == "1") { ?>
		<tr><td></td>
		<td><?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($id, $this->row->sermon_path); ?></td>
		</tr>
	<?php }
	if ($this->row->addfile && $this->row->addfileDesc) { ?>
		<tr>
			<td valign="top"><b><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:</b></td>
			<td><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->row->addfile, $this->row->addfileDesc); ?></td>
		</tr>
	<?php } ?>
</table>
<?php 
/*
// Code from Douglas Machado
// Shows Tags on bottom of site if some are present.
// experimental
	$keywords=explode(',',$this->row->metakey);
	$keyTotal = count($keywords);
	if ($keyTotal > '1'){
		$rowCount = 1;
		$html = "<label>Tags: </label>\r";
		foreach($keywords as $keyword){
			$keyword = trim($keyword);
			$html .= '<a href="'.JRoute::_("index.php?option=com_search&ordering=newest&searchphrase=all&searchword=".$keyword).'" >'.$keyword.'</a>';
			if($keyTotal != $rowCount) $html .= ', ';
			$rowCount++;
		}
		echo $html;
	}
*/
?>
<table width="100%">
	<?php
	// Support for JComments
	$comments = JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php';
	if (file_exists($comments)) {
		require_once($comments); ?>
		<tr><td><br></td></tr>
		<tr>
			<td>
				<?php echo JComments::showComments($id, 'com_sermonspeaker', $this->row->sermon_title); ?>
			</td>
		</tr>
	<?php } ?>
</table>
</div>