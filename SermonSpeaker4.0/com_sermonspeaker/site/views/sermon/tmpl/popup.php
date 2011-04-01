<?php
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
	window.onload = applyChanges()
	function applyChanges(){
		window.resizeTo(<?php echo $this->player['width'].', '.$this->player['height']; ?>);
		document.body.style.backgroundColor='<?php echo $this->params->get('popup_color', '#fff'); ?>';
	}
</script>
<div class="ss-sermon-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
	<div class="popup">
		<h2><?php echo $this->item->sermon_title; ?></h2>
		<?php 
		echo $this->player['mspace'];
		echo $this->player['script'];
		?>
	</div>
</div>