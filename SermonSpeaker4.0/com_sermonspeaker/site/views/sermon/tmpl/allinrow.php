<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$player = new SermonspeakerHelperPlayer($this->item);
?>
<div class="ss-sermon-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($this->item->slug)); ?>"><?php echo $this->item->sermon_title; ?></a></h2>
<!-- Begin Header -->
<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<?php if (in_array('sermon:scripture', $this->columns) && $this->item->sermon_scripture) : ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?></th>
		<?php endif;
		if (in_array('sermon:notes', $this->columns) && strlen($this->item->notes) > 0) : ?>
			<th align="left" valign="bottom"> <?php echo JText::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL'); ?></th>
		<?php endif;
		if (in_array('sermon:addfile', $this->columns) && $this->item->addfile) : ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></th>
		<?php endif;
		if (in_array('sermon:player', $this->columns)) : ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_PLAYER'); ?></th>
		<?php endif; ?>
	</tr> 
<!-- Begin Data -->
	<tr>
		<?php if (in_array('sermon:scripture', $this->columns) && $this->item->sermon_scripture) : ?>
			<td align="left" valign="top"><?php echo JHTML::_('content.prepare', $this->item->sermon_scripture); ?></td>
		<?php endif;
		if (in_array('sermon:notes', $this->columns) && strlen($this->item->notes) > 0) : ?>
			<td align="left" valign="top"><?php echo JHTML::_('content.prepare', $this->item->notes); ?></td>
		<?php endif;
		if (in_array('sermon:addfile', $this->columns) && $this->item->addfile) : ?>
			<td align="left" valign="top"><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?></td>
		<?php endif;
		if (in_array('sermon:player', $this->columns)) : ?> 
			<td align="center" valign="top">
				<?php if (!$player->status): ?>
					<span class="no_entries"><?php echo $player->error; ?></span>
				<?php else:
					echo $player->mspace;
					echo $player->script;
				endif;
				if ($player->toggle): ?>
					<div class="ss-sermon-switch">
						<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
						<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
					</div>
				<?php endif; ?>
			</td>
		<?php endif; ?>
	</tr>
</table>
<div style="float:left;">
	<?php if (in_array('sermon:download', $this->columns) && $player->file) :
		echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->slug, $player->status);
	endif; ?>
</div>
<div style="float:right;">
	<?php if ($this->params->get('popup_player') == "1" && strlen($this->item->audiofile) > 0) :
		echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->item->id, $player);
	endif; ?>
</div>
<?php
if ($this->params->get('enable_keywords')):
	$tags = SermonspeakerHelperSermonspeaker::insertSearchTags($this->item->metakey); 
	if ($tags): ?>
		<div class="tag"><?php echo JText::_('COM_SERMONSPEAKER_TAGS').' '.$tags; ?></div>
	<?php endif;
endif;
// Support for JComments
$comments = JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php';
if ($this->params->get('enable_jcomments') && file_exists($comments)) : ?>
	<div class="jcomments">
		<?php
		require_once($comments);
		echo JComments::showComments($this->item->id, 'com_sermonspeaker', $this->item->sermon_title); ?>
	</div>
<?php endif; ?>
</div>