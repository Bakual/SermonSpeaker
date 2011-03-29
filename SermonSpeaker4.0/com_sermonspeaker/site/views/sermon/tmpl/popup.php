<?php
defined('_JEXEC') or die('Restricted access');
$player = SermonspeakerHelperSermonspeaker::insertPlayer($this->item, $this->speaker->name);
?>
<script type="text/javascript">
	window.onload = applyChanges()
	function applyChanges(){
		window.resizeTo(<?php echo $player['width'].', '.$player['height']; ?>);
		document.body.style.backgroundColor='<?php echo $this->params->get('popup_color', '#fff'); ?>';
	}
</script>
<div class="popup">
	<h3 class="contentheading"><?php echo $this->item->sermon_title; ?></h3>
	<?php 
	echo $player['mspace'];
	echo $player['script'];
	?>
</div>