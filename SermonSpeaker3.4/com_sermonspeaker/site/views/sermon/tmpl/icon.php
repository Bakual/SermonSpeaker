<?php
defined('_JEXEC') or die('Restricted access');
if ($this->params->get('ga')) { $callback = "&callback=".$this->params->get('ga'); }
$return = SermonspeakerHelperSermonspeaker::insertAddfile($this->row->addfile, $this->row->addfileDesc);
$id = $this->row->id;
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>
<div id="sermon-container">
	<h1 class="componentheading"><a 
		title="<?php echo JText::_('COM_SERMONSPEAKER_SERMONTITLE').':: '.$this->escape($this->row->sermon_title); ?>" 
		href="<?php echo JRoute::_('index.php?view=sermon&id='.$this->row->slug); ?>"><?php echo $this->escape($this->row->sermon_title); ?></a></h1>
	<div id="sermon-infobox">
		<?php if ($this->params->get('client_col_player')){
			$playerPath	= JURI::base().'components/com_sermonspeaker/media/player/1pixelout/';
			$doc	=& JFactory::getDocument();
			$doc->addScript($playerPath.'audio-player.js'); ?>
			<div id="sermon-player-container">
				<div class="ss-player">
					<object type="application/x-shockwave-flash" data="<?php echo $playerPath; ?>player.swf" id="audioplayer1" height="24" width="290">
						<param name="movie" value="<?php echo $playerPath;	?>player.swf">
						<param name="FlashVars" value="playerID=audioplayer1&soundFile=<?php echo $this->lnk; ?>">
						<param name="quality" value="high">
						<param name="menu" value="false">
						<param name="wmode" value="transparent">
					</object>
				</div>
				<?php if ($this->params->get('popup_player') || $this->params->get('dl_button')){ ?>
					<div class="ss-mp3-links">
					<?php if ($this->params->get('popup_player')){ ?>
						<a href="<?php echo JURI::current(); ?>" class="new-window" onclick="popup = window.open('<?php echo JRoute::_("index.php?view=sermon&layout=popup&id=$id&tmpl=component"); ?>', 'PopupPage', 'height=300,width=350,scrollbars=yes,resizable=yes'); return false"><?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?></a>
					<?php }
					if ($this->params->get('popup_player') && $this->params->get('dl_button')){ ?>
						<br />
					<?php }
					if ($this->params->get('dl_button')){ ?>
						<a href="<?php echo $this->lnk; ?>" class="download"><?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON'); ?></a>
					<?php } ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
		<div class="ss-fields-container">
			<div class="ss-field field-speaker" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>">
				<?php echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($this->speaker->id, $this->speaker->pic, $this->speaker->name); ?>
			</div>
			<?php if ($this->params->get('client_col_sermon_scripture_reference') && $this->row->sermon_scripture){ ?>
				<div class="ss-field field-bible" title="<?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?>">
					<?php echo $this->row->sermon_scripture; ?>
				</div>
			<?php } ?>
		</div>
		<div class="ss-fields-container">
			<?php if ($this->params->get('client_col_sermon_date') && ($this->row->sermon_date != "0000-00-00")){ ?>
				<div class="ss-field field-calendar" title="<?php echo JText::_('COM_SERMONSPEAKER_SERMONDATE'); ?>">
					<?php echo JHTML::date($this->row->sermon_date, JText::_('DATE_FORMAT_LC1'), 0); ?>
				</div>
			<?php }
			if ($this->params->get('client_col_sermon_time') && ($this->row->sermon_time != "00:00:00")){ ?>
				<div class="ss-field field-time" title="<?php echo JText::_('COM_SERMONSPEAKER_SERMONTIME'); ?>">
					<?php echo SermonspeakerHelperSermonspeaker::insertTime($this->row->sermon_time); ?>
				</div>
			<?php } ?>
		</div>
		<div class="ss-fields-container">
			<?php if ($this->params->get('client_col_sermon_series') && $this->serie->series_title){ ?>
				<div class="ss-field field-series" title="<?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>">
					<a href="<?php echo JRoute::_('index.php?view=serie&id='.$this->serie->id); ?>"><?php 
						echo $this->escape($this->serie->series_title); ?></a>
				</div>
			<?php }
			if ($this->params->get('client_col_sermon_addfile') && $this->row->addfile && $this->row->addfileDesc) { ?>
				<div class="ss-field field-addfile" title="<?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>">
					<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->row->addfile, $this->row->addfileDesc); ?>
				</div>
			<?php } ?>
		</div>
		<br style="clear:both" />
	</div>
</div>
<?php if ($this->params->get('client_col_sermon_notes') && strlen($this->row->notes) > 0){ ?>
	<div class="ss-notes">
		<?php echo $this->row->notes; ?>
	</div>
<?php }
$keywords = explode(',', $this->row->metakey);
$keyTotal = count($keywords);
$rowCount = 1;
$html = '';
if ($keywords[0]){ ?>
	<div class="tag">Tags: <?php
		foreach($keywords as $keyword){
			$keyword = trim($keyword);
			$html .= '<a href="'.JRoute::_("index.php?option=com_search&ordering=newest&searchphrase=all&searchword=".$keyword).'" >'.$keyword.'</a>';
			if($keyTotal != $rowCount) $html .= ', ';
			$rowCount++;
		}
		echo $html; ?>
	</div>
<?php }
if ($this->row->hits){ ?>
	<div class="hits" title="<?php echo JText::_('COM_SERMONSPEAKER_HITS'); ?>">
		<?php echo JText::_('COM_SERMONSPEAKER_HITS').': '.$this->row->hits; ?>
	</div>
<?php } ?>
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