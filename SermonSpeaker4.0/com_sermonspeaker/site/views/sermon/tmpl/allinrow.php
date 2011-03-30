<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>
<div id="ss-sermon-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_TITLE'); ?></h1>
<?php if (!$this->params->get('hide_dl') && $this->player['file']) : ?>
	<h3 class="contentheading">
		<a title="<?php echo JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER'); ?>" href="<?php echo $this->player['file']; ?>"><?php echo $this->item->sermon_title; ?></a>
	</h3>
<?php else : ?>
	<h3 class="contentheading"><?php echo $this->item->sermon_title; ?></h3>
<?php endif; ?>
<!-- Begin Header -->
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
				<?php if ($this->player['status'] == 'error'): ?>
					<span class="no_entries"><?php echo $this->player['error']; ?></span>
				<?php else:
					echo $this->player['mspace'];
					echo $this->player['script'];
				endif; ?>
			</td>
		<?php endif; ?>
	</tr>
</table>
<div style="float:left;">
	<?php if ($this->params->get('dl_button') && $this->player['file']) :
		echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->id, $this->player['file']);
	endif; ?>
</div>
<div style="float:right;">
	<?php if ($this->params->get('popup_player') == "1" && strlen($this->item->audiofile) > 0) :
		echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->item->id, $this->player);
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
</table>
</div>