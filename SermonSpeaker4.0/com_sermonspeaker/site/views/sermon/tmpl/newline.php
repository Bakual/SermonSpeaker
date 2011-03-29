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
<!-- Begin Data -->
<div class="ss-sermondetail-container">
	<?php if (in_array('sermon:scripture', $this->columns) && $this->item->sermon_scripture) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo JHTML::_('content.prepare', $this->item->sermon_scripture); ?></div>
	<?php endif;
	if (in_array('sermon:player', $this->columns)) : ?>
		<div class="ss-sermondetail-text ss-sermon-player">
			<?php if ($this->player['status'] == 'error'): ?>
				<span class="no_entries"><?php echo $this->player['error']; ?></span>
			<?php else:
				echo $this->player['mspace'];
				echo $this->player['script'];
			endif; ?>
		</div>
	<?php endif;
	if ($this->params->get('dl_button') && ($this->player['status'] != 'error')) : ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->id, $this->item->audiofile); ?></div>
	<?php endif;
	if ($this->params->get('popup_player') && $this->player['file']) : ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->item->id, $this->player); ?></div>
	<?php endif;
	if (in_array('sermon:addfile', $this->columns) && $this->item->addfile) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:</div>
		<div class="ss-sermondetail-text">
			<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?>
		</div>
	<?php endif;
	if (in_array('sermon:notes', $this->columns) && strlen($this->item->notes) > 0) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo JHTML::_('content.prepare', $this->item->notes); ?></div>
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