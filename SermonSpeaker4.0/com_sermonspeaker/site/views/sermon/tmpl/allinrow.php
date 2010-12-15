<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$columns = $this->params->get('col');
if (!$columns){
	$columns = array();
}
?>
<div id="ss-sermon-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_TITLE'); ?></h1>
<!-- Begin Header -->
<h3 class="contentheading">
<?php if ($this->params->get('hide_dl') == "0" && strlen($this->item->sermon_path) > 0) : ?>
	<a title='<?php echo JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER'); ?>' href='<?php echo $this->lnk; ?>'><?php echo $this->item->sermon_title; ?></a>
<?php else :
	echo $this->item->sermon_title;
endif; ?>
</h3>
<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<?php if (in_array('sermon:scripture', $columns) && $this->item->sermon_scripture) : ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?></th>
		<?php endif;
		if (in_array('sermon:notes', $columns) && strlen($this->item->notes) > 0) : ?>
			<th align="left" valign="bottom"> <?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?></th>
		<?php endif;
		if (in_array('sermon:addfile', $columns) && $this->item->addfile && $this->item->addfileDesc) : ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></th>
		<?php endif;
		if (in_array('sermon:player', $columns) && strlen($this->item->sermon_path) > 0) : ?>
			<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_PLAYER'); ?></th>
		<?php endif; ?>
	</tr> 
<!-- Begin Data -->
	<tr>
		<?php if (in_array('sermon:scripture', $columns) && $this->item->sermon_scripture) : ?>
			<td align="left" valign="top"><?php echo $this->item->sermon_scripture; ?></td>
		<?php endif;
		if (in_array('sermon:notes', $columns) && strlen($this->item->notes) > 0) : ?>
			<td align="left" valign="top"><?php echo $this->item->notes; ?></td>
		<?php endif;
		if (in_array('sermon:addfile', $columns) && $this->item->addfile && $this->item->addfileDesc) : ?>
			<td align="left" valign="top"><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?></td>
		<?php endif;
		if (in_array('sermon:player', $columns) && strlen($this->item->sermon_path) > 0) : ?> 
			<td align="center" valign="top">
				<?php
				$ret = SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk, $this->item->sermon_time, 1, $this->item->sermon_title, $this->speaker->name);
				?>
			</td>
		<?php endif; ?>
	</tr>
	<?php $this->lnk = str_replace('\\','/',$this->lnk); ?>
</table>
<div style="float:left;">
	<?php if ($this->params->get('dl_button') == "1" && strlen($this->item->sermon_path) > 0) :
		echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->id, $this->item->sermon_path);
	endif; ?>
</div>
<div style="float:right;">
	<?php if ($this->params->get('popup_player') == "1" && strlen($this->item->sermon_path) > 0) :
		echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->item->id, $ret);
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