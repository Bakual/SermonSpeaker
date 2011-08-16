<?php
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
	window.onload = applyChanges()
	function applyChanges(){
		window.resizeTo(<?php echo $this->player->popup['width'].', '.$this->player->popup['height']; ?>);
		document.body.style.backgroundColor='<?php echo $this->params->get('popup_color', '#fff'); ?>';
	}
</script>
<div class="ss-sermon-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
	<div class="popup">
		<h2><?php echo $this->item->sermon_title; ?></h2>
		<?php 
		echo $this->player->mspace;
		echo $this->player->script;
		if ($this->player->toggle): ?>
			<div class="ss-sermon-switch">
				<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
				<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
			</div>
		<?php endif; ?>
	</div>
</div>