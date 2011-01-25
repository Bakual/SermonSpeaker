<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$player = SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk, $this->item->sermon_time, 1, $this->item->sermon_title, $this->speaker->name);
?>
<div id="ss-sermon-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_TITLE'); ?></h1>
<!-- Begin Data -->
<?php if ($this->params->get('hide_dl') == "0" && strlen($this->item->sermon_path) > 0) :
	echo '<h3 class="contentheading"><a title="'.JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER').'" href="'.$this->lnk."\">".$this->item->sermon_title.'</a></h3>';
else :
	echo '<h3 class="contentheading">'.$this->item->sermon_title.'</h3>';
endif; ?>
<div class="ss-sermondetail-container">
	<?php if (in_array('sermon:scripture', $this->columns) && $this->item->sermon_scripture) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo $this->item->sermon_scripture; ?></div>
	<?php endif;
	if (in_array('sermon:player', $this->columns) && strlen($this->item->sermon_path) > 0) : ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text ss-sermon-player">
			<?php 
			echo $player['mspace'];
			echo $player['script'];
			?>
		</div>
	<?php endif;
	$this->lnk = str_replace('\\','/',$this->lnk);
	if ($this->params->get('dl_button') == "1" && strlen($this->item->sermon_path) > 0) : ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->id, $this->item->sermon_path); ?></div>
	<?php endif;
	if ($this->params->get('popup_player') == "1" && strlen($this->item->sermon_path) > 0) : ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->item->id, $player); ?></div>
	<?php endif;
	if (in_array('sermon:addfile', $this->columns) && $this->item->addfile) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:</div>
		<div class="ss-sermondetail-text">
			<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?>
		</div>
	<?php endif;
	if (in_array('sermon:notes', $this->columns) && strlen($this->item->notes) > 0) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo $this->item->notes; ?></div>
	<?php endif; ?>
</div>
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
</div>