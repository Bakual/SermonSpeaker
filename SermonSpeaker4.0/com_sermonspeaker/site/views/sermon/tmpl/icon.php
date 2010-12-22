<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$player = SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk, $this->item->sermon_time, 1, $this->item->sermon_title, $this->speaker->name);
?>
<div id="sermon-container">
	<h1 class="componentheading"><a 
		title="<?php echo JText::_('COM_SERMONSPEAKER_SERMONTITLE').':: '.$this->escape($this->item->sermon_title); ?>" 
		href="<?php echo JRoute::_('index.php?view=sermon&id='.$this->item->slug); ?>"><?php echo $this->escape($this->item->sermon_title); ?></a></h1>
	<div id="sermon-infobox">
		<div id="sermon-player-container">
			<?php if (in_array('sermon:player', $this->columns)) : ?>
				<div class="ss-player">
				<?php 
				echo $player['mspace'];
				echo $player['script'];
				?>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('popup_player') || $this->params->get('dl_button')) : ?>
				<div class="ss-mp3-links">
				<?php if ($this->params->get('popup_player')) : ?>
					<a href="<?php echo JURI::current(); ?>" class="new-window" onclick="popup = window.open('<?php echo JRoute::_('index.php?view=sermon&layout=popup&id='.$this->item->id.'&tmpl=component'); ?>', 'PopupPage', 'height=300,width=350,scrollbars=yes,resizable=yes'); return false"><?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?></a>
				<?php endif;
				if ($this->params->get('popup_player') && $this->params->get('dl_button')) : ?>
					<br />
				<?php endif;
				if ($this->params->get('dl_button')) :
					//Check if link targets to an external source
					if (substr($this->item->sermon_path, 0, 7) == 'http://') : //File is external
						$fileurl = $this->item->sermon_path;
					else : //File is locally 
						$fileurl = JURI::root().'index.php?option=com_sermonspeaker&amp;task=download&amp;id='.$this->item->id;
					endif; ?>
					<a href="<?php echo $fileurl; ?>" class="download">
						<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON'); ?>
					</a>
				<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="ss-fields-container">
			<?php if (in_array('sermon:speaker', $this->columns)): ?>
				<div class="ss-field field-speaker" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>">
					<?php echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($this->speaker->slug, $this->speaker->pic, $this->speaker->name); ?>
				</div>
			<?php endif; ?>
			<?php if (in_array('sermon:scripture', $this->columns) && $this->item->sermon_scripture) : ?>
				<div class="ss-field field-bible" title="<?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?>">
					<?php echo $this->item->sermon_scripture; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="ss-fields-container">
			<?php if (in_array('sermon:date', $this->columns) && ($this->item->sermon_date != "0000-00-00")) : ?>
				<div class="ss-field field-calendar" title="<?php echo JText::_('COM_SERMONSPEAKER_SERMONDATE'); ?>">
					<?php echo JHTML::date($this->item->sermon_date, JText::_('DATE_FORMAT_LC1')); ?>
				</div>
			<?php endif;
			if (in_array('sermon:length', $this->columns) && ($this->item->sermon_time != "00:00:00")) : ?>
				<div class="ss-field field-time" title="<?php echo JText::_('COM_SERMONSPEAKER_SERMONLENGTH'); ?>">
					<?php echo SermonspeakerHelperSermonspeaker::insertTime($this->item->sermon_time); ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="ss-fields-container">
			<?php if (in_array('sermon:series', $this->columns) && $this->serie->series_title) : ?>
				<div class="ss-field field-series" title="<?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>">
					<a href="<?php echo JRoute::_('index.php?view=serie&id='.$this->serie->slug); ?>"><?php 
						echo $this->escape($this->serie->series_title); ?></a>
				</div>
			<?php endif;
			if (in_array('sermon:addfile', $this->columns) && $this->item->addfile) : ?>
				<div class="ss-field field-addfile" title="<?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>">
					<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?>
				</div>
			<?php endif; ?>
		</div>
		<br style="clear:both" />
	</div>
</div>
<?php if (in_array('sermon:notes', $this->columns) && strlen($this->item->notes) > 0) : ?>
	<div class="ss-notes">
		<?php echo $this->item->notes; ?>
	</div>
<?php endif;
$keywords = explode(',', $this->item->metakey);
$keyTotal = count($keywords);
$rowCount = 1;
$html = '';
if ($keywords[0]): ?>
	<div class="tag">Tags: <?php
		foreach($keywords as $keyword) :
			$keyword = trim($keyword);
			$html .= '<a href="'.JRoute::_("index.php?option=com_search&ordering=newest&searchphrase=all&searchword=".$keyword).'" >'.$keyword.'</a>';
			if($keyTotal != $rowCount) $html .= ', ';
			$rowCount++;
		endforeach;
		echo $html; ?>
	</div>
<?php endif;
if ($this->item->hits) : ?>
	<div class="hits" title="<?php echo JText::_('COM_SERMONSPEAKER_HITS'); ?>">
		<?php echo JText::_('COM_SERMONSPEAKER_HITS').': '.$this->item->hits; ?>
	</div>
<?php endif; ?>
<table width="100%">
	<?php
	// Support for JComments
	$comments = JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php';
	if (file_exists($comments)) :
		require_once($comments); ?>
		<tr><td><br /></td></tr>
		<tr>
			<td>
				<?php echo JComments::showComments($this->item->id, 'com_sermonspeaker', $this->item->sermon_title); ?>
			</td>
		</tr>
	<?php endif; ?>
</table>