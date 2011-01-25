<?php
defined('_JEXEC') or die('Restricted access');
if ($this->params->get('popup_color')) :
	$bgcolor = 'background-color:#'.$this->params->get('popup_color').';';
endif;
$player = SermonspeakerHelperSermonspeaker::insertPlayer($this->item, $this->speaker->name);
?>
<body style="<?php echo $bgcolor; ?>">
<div class="popup">
	<h3 class="contentheading"><?php echo $this->item->sermon_title; ?></h3>
	<?php 
	echo $player['mspace'];
	echo $player['script'];
	?>
</div>