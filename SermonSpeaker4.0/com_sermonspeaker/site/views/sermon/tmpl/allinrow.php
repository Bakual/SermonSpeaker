<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$player = SermonspeakerHelperSermonspeaker::insertPlayer($this->item, $this->speaker->name);
?>
<div id="ss-sermon-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_TITLE'); ?></h1>
<!-- Begin Header -->
<h3 class="contentheading">
<?php if ($this->params->get('hide_dl') == "0" && strlen($this->item->audiofile) > 0) : ?>
	<a title='<?php echo JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER'); ?>' href='<?php echo $this->lnk; ?>'><?php echo $this->item->sermon_title; ?></a>
<?php else :
	echo $this->item->sermon_title;
endif; ?>
</h3>
<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<?php if (in_array('sermon:scripture', $this->columns) && $this->item->sermon_scripture) : ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?></th>
		<?php endif;
		if (in_array('sermon:notes', $this->columns) && strlen($this->item->notes) > 0) : ?>
			<th align="left" valign="bottom"> <?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?></th>
		<?php endif;
		if (in_array('sermon:addfile', $this->columns) && $this->item->addfile) : ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></th>
		<?php endif;
		if (in_array('sermon:player', $this->columns) && strlen($this->item->audiofile) > 0) : ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_PLAYER'); ?></th>
		<?php endif; ?>
	</tr> 
<!-- Begin Data -->
	<tr>
		<?php if (in_array('sermon:scripture', $this->columns) && $this->item->sermon_scripture) : ?>
			<td align="left" valign="top"><?php echo $this->item->sermon_scripture; ?></td>
		<?php endif;
		if (in_array('sermon:notes', $this->columns) && strlen($this->item->notes) > 0) : ?>
			<td align="left" valign="top"><?php echo $this->item->notes; ?></td>
		<?php endif;
		if (in_array('sermon:addfile', $this->columns) && $this->item->addfile) : ?>
			<td align="left" valign="top"><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?></td>
		<?php endif;
		if (in_array('sermon:player', $this->columns) && strlen($this->item->audiofile) > 0) : ?> 
			<td align="center" valign="top">
				<?php 
				echo $player['mspace'];
				echo $player['script'];
				?>
			</td>
		<?php endif; ?>
	</tr>
</table>
<div style="float:left;">
	<?php if ($this->params->get('dl_button') == "1" && strlen($this->item->audiofile) > 0) :
		echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->id, $this->item->audiofile);
	endif; ?>
</div>
<div style="float:right;">
	<?php if ($this->params->get('popup_player') == "1" && strlen($this->item->audiofile) > 0) :
		echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->item->id, $player);
	endif; ?>
</div>
<table width="100%" style="clear:both;">
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